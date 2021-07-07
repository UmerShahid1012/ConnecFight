<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\ChatMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function create(Request $request){
//        dd($request->all());
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|numeric|min:0|not_in:0',
            'type' => 'required',
        ]);
        if($validator->fails()){
            return sendError($validator->getMessageBag(),null);
        }

        $check = Chat::where(['sender_id'=>Auth::id(),'receiver_id'=>$request->receiver_id])->orWhere(['sender_id'=>$request->receiver_id,'receiver_id'=>Auth::id()])->first();

        if ($check){
            $validator = Validator::make($request->all(), [
                'type' => 'required',
                ]);
            if($validator->fails()){
                return sendError($validator->getMessageBag(),null);
            }
            if($request->type =='text' && !$request->message){
                return sendError('Message required', null);
            }
            if($request->type =='video' && !$request->has('media')){
                return sendError('Video required ', null);
            }
            if($request->type =='image' && !$request->has('media')){
                return sendError('Image required ', null);
            }



            if($check->sender_id == Auth::id()){
                $sender_id = $check->sender_id;
                $receiver_id = $check->receiver_id;
            }else{
                $sender_id = $check->receiver_id;
                $receiver_id = $check->sender_id;
            }
            $message = new ChatMessage();
            $message->chat_id = $check->id;
            $message->sender_id = $sender_id;
            $message->receiver_id = $receiver_id;
            if ($request->media != '' && $request->media != null){

                $message->path = $request->media;
                $type = $request->type;
            }
            if($request->message){
                $message->message = $request->message;
            }

            if(isset($type)){
                $message->type = $type;
            }else{
                $message->type = $request->type;
            }

            $message->is_read = '0';
            $message->save();

            $check->last_message_id = $message->id;
            $check->save();

            $data['message'] = ChatMessage::where('id',$message->id)->with('sender','receiver')->first();
            $data['chat'] = $check;
            $sender = User::where('id',$sender_id)->first();
            $check_noti = Notification::where('chat_id',$check->id)->where('type','message')->where('on_user',$receiver_id)->first();
            if($check_noti){
                $check_noti->is_read = 0;
                $check_noti->save();
                $data['notification'] = $check_noti;
                $noti_con = new NotificationController;
                $noti_con->sendPushNotification([$receiver_id],$check_noti->notification_text, $check_noti);

            }else{
                $notification =  new Notification();
                $notification->sender_name = $sender->first_name.' '.$sender->last_name;
                $notification->user_id = $sender->id;
                $notification->on_user = $receiver_id;
                $notification->type = 'message';
                $notification->notification_text = $sender->first_name.' '.$sender->last_name.' has send you a message.';
                $notification->profile_photo = $sender->profile_image;
                $notification->chat_id = $check->id;
                $notification->save();
                $data['notification'] = $notification;
                $noti_con = new NotificationController;
                $noti_con->sendPushNotification([$receiver_id],$notification->notification_text, $notification);

            }



        }else {

//            dd(1);
            $chat = new Chat();
            $chat->sender_id = Auth::id();
            $chat->receiver_id = $request->receiver_id;

            if ($chat->save()) {
                $chat1 = Chat::where('id', $chat->id)->with('sender', 'receiver')->first();
                $user = User::where('id', Auth::id())->first();
                if ($request->has('type')) {
                    $validator = Validator::make($request->all(), [
                        'type' => 'required',
                    ]);
                    if ($validator->fails()) {
                        return sendError($validator->getMessageBag(), null);
                    }
                }
                if($request->type =='text' && !$request->message){
                    return sendError('Message required', null);
                }
                if($request->type =='video' && !$request->has('media')){
                    return sendError('Video required ', null);
                }
                if($request->type =='image' && !$request->has('media')){
                    return sendError('Image required ', null);
                }


                if($chat1->sender_id == Auth::id()){
                    $sender_id = $chat1->sender_id;
                    $receiver_id = $chat1->receiver_id;
                }else{
                    $sender_id = $chat1->receiver_id;
                    $receiver_id = $chat1->sender_id;
                }
                $message = new ChatMessage();
                $message->chat_id = $chat1->id;
                $message->sender_id = $sender_id;
                $message->receiver_id = $receiver_id;
                if ($request->media != '' && $request->media != null){

                    $message->path = $request->media;
                    $type = $request->type;
                }
                if($request->message){
                    $message->message = $request->message;
                }

                if(isset($type)){
                    $message->type = $type;
                }else{
                    $message->type = $request->type;
                }

                $message->is_read = '0';
                $message->save();

                $chat1->last_message_id = $message->id;
                $chat1->save();

                $data['message'] = ChatMessage::where('id',$message->id)->with('sender','receiver')->first();
                $data['chat'] = $chat1;
                $sender = User::where('id',$sender_id)->first();

                    $notification =  new Notification();
                    $notification->sender_name = $sender->first_name.' '.$sender->last_name;
                    $notification->user_id = $sender->id;
                    $notification->on_user = $receiver_id;
                    $notification->type = 'message';
                    $notification->notification_text = $sender->first_name.' '.$sender->last_name.' has send you a message.';
                    $notification->profile_photo = $sender->profile_image;
                    $notification->chat_id = $chat1->id;
                    $notification->save();
                    $data['notification'] = $notification;
                    $noti_con = new NotificationController;
                    $noti_con->sendPushNotification([$receiver_id],$notification->notification_text, $notification);


                $data['notification'] = Notification::where('id', $notification->id)->first();

                $noti_con = new NotificationController;
                $noti_con->sendPushNotification([$request->receiver_id], $notification->notification_text, $data['notification']);

            } else {
                return sendError('Something went wrong', null);
            }
        }

        return sendSuccess('Message added successfully.',$data);

    }

    public function addMessage(Request $request){

        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|numeric|min:0|not_in:0',
//            'receiver_id' => 'required|numeric|min:0|not_in:0',
            'type' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,svg|max:2048',

        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        if($request->type =='text' && !$request->message){
            return sendError('Message required', null);
        }
        if($request->type =='image' || $request->type =='video' && !$request->has('media')){
            return sendError('Image required', null);
        }

        $chat = Chat::where('id',$request->chat_id)->with('sender','receiver')->first();

        if($chat->sender_id == Auth::id()){
            $sender_id = $chat->sender_id;
            $receiver_id = $chat->receiver_id;
        }else{
            $sender_id = $chat->receiver_id;
            $receiver_id = $chat->sender_id;
        }
        $message = new ChatMessage();
        $message->chat_id = $request->chat_id;
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        if ($request->image != '' && $request->image != null && $request->hasFile('image')){
            $thumb = $request->file('image');
            $thumbname = pathinfo($thumb, PATHINFO_FILENAME);
            $thumb_name = $thumbname.rand(1000, 9999) . '.' . $thumb->getClientOriginalExtension();
            $uploaded_path = public_path() . '/images/chat';
            $thumb->move($uploaded_path, $thumb_name);
            $message->path = 'public/images/chat/'.$thumb_name;
            $type = 'image';
        }
        if($request->message){
            $message->message = $request->message;
        }

        if($request->share_type){
            $message->share_type = $request->share_type;
        }

        if($request->share_id){
            $message->share_id = $request->share_id;
        }

        if(isset($type)){
            $message->type = $type;
        }else if($request->type){
            $message->type = $request->type;
        }

        $message->is_read = '0';
        $message->save();

        $chat->last_message_id = $message->id;
        $chat->save();

        $data['message'] = ChatMessage::where('id',$message->id)->with('sender','receiver')->first();
        $data['chat'] = $chat;
        $sender = User::where('id',$sender_id)->first();
        $check_noti = Notification::where('chat_id',$chat->id)->where('type','message')->where('on_user',$receiver_id)->first();
        if($check_noti){
            $check_noti->is_read = 0;
            $check_noti->save();
            $data['notification'] = $check_noti;
            $noti_con = new NotificationController;
            $noti_con->sendPushNotification([$receiver_id],$check_noti->notification_text, $check_noti);

        }else{
            $notification =  new Notification();
            $notification->sender_name = $sender->first_name.' '.$sender->last_name;
            $notification->user_id = $sender->id;
            $notification->on_user = $receiver_id;
            $notification->type = 'message';
            $notification->notification_text = $sender->first_name.' '.$sender->last_name.' has send you a message.';
            $notification->profile_photo = $sender->profile_image;
            $notification->chat_id = $chat->id;
            $notification->save();
            $data['notification'] = $notification;
            $noti_con = new NotificationController;
            $noti_con->sendPushNotification([$receiver_id],$notification->notification_text, $notification);

        }




        return sendSuccess('Message added successfully.',$data);

    }

    public function share(Request $request){

        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|numeric|min:0|not_in:0',
            'share_type' => 'required',
            'share_id' => 'required',

        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        $chat = Chat::where('id',$request->chat_id)->with('sender','receiver')->first();

        if($chat->sender_id == Auth::id()){
            $sender_id = $chat->sender_id;
            $receiver_id = $chat->receiver_id;
        }else{
            $sender_id = $chat->receiver_id;
            $receiver_id = $chat->sender_id;
        }
        $message = new ChatMessage();
        $message->chat_id = $request->chat_id;
        $message->sender_id = $sender_id;
        $message->receiver_id = $receiver_id;
        if($request->message){
            $message->message = $request->message;
        }

        $message->is_read = '0';
        $message->share_type = $request->share_type;
        $message->share_id = $request->share_id;
        $message->save();

        $chat->last_message_id = $message->id;
        $chat->save();

        $data['message'] = ChatMessage::where('id',$message->id)->with('sender','receiver')->first();
        $data['chat'] = $chat;
        $sender = User::where('id',$sender_id)->first();
        $check_noti = Notification::where('chat_id',$chat->id)->where('type','message')->where('on_user',$receiver_id)->first();
        if($check_noti){
            $check_noti->is_read = 0;
            $check_noti->save();
            $data['notification'] = $check_noti;
            $noti_con = new NotificationController;
            $noti_con->sendPushNotification([$receiver_id],$check_noti->notification_text, $check_noti);

        }else{
            $notification =  new Notification();
            $notification->sender_name = $sender->first_name.' '.$sender->last_name;
            $notification->user_id = $sender->id;
            $notification->on_user = $receiver_id;
            $notification->type = 'message';
            $notification->notification_text = $sender->first_name.' '.$sender->last_name.' has send you a message.';
            $notification->profile_photo = $sender->profile_image;
            $notification->chat_id = $chat->id;
            $notification->save();
            $data['notification'] = $notification;
            $noti_con = new NotificationController;
            $noti_con->sendPushNotification([$receiver_id],$notification->notification_text, $notification);

        }
        return sendSuccess('Message added successfully.',$data);

    }


    public function getChat(Request $request){

        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|numeric|min:0|not_in:0',
        ]);
        if($validator->fails()){
            return sendError($validator->getMessageBag(), null);
        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;

        $user_id = Auth::id();
        $chat = Chat::where('id',(int)$request->chat_id)->with('sender','receiver','getCounterNotification')->first();


        if($chat){

            ChatMessage::where('chat_id',$request->chat_id)->where('receiver_id',Auth::id())->update(['is_read' => 1]);
//            $data['message'] = Message::where('chat_id',$request->chat_id)->with('sender','receiver')->get();
            $data['message'] = ChatMessage::with('sender','sender.stance_id', 'receiver','receiver.stance_id')->where('chat_id',$chat->id)
                ->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0")->orderBy('created_at','desc')
                ->paginate($per_page);
//            dd($data['message']);
//            $data['chat'] = $chat;

            return sendSuccess('Got message successfully.',$data);
        }else {
            return sendError('Invalid chat id', null);
        }


    }

    public function seenMessages(Request $request){

        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|numeric|min:0|not_in:0',
//            'last_message_id' => 'required|numeric|min:0|not_in:0',
            'chat_id' => 'required|numeric|min:0|not_in:0',
        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        $messages =  ChatMessage::where('chat_id',$request->chat_id)->where('receiver_id',$request->receiver_id)->where('is_read',0)->get();

        if(!$messages->isEmpty()){

            foreach($messages as $message){
                $message->is_read = 1;
                $message->save();
            }
//            $messages->update(['is_read' => 1]);
//            $messages->save();

            return sendSuccess('Messages read successfully',null);
        }else{
            return sendError('No unread messages found with this chat id', null);
        }
    }
    public function deleteMsg(Request $request){

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|numeric|min:0|not_in:0',
//            'last_message_id' => 'required|numeric|min:0|not_in:0',
            'message_id' => 'required|numeric|min:0|not_in:0',
        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        $messages =  ChatMessage::where('id',$request->message_id)->first();

        if(!$messages){

            return sendError('No message found!', null);
        }

        if (ChatMessage::where(['id'=>$request->message_id,'sender_id'=>$request->deleted_by,'sender_deleted'=>0])->first()){

            ChatMessage::where(['id'=>$request->message_id])->update(['sender_deleted'=>1]);
            return sendSuccess('Message deleted successfully!',null);

        }elseif (ChatMessage::where(['id'=>$request->message_id,'receiver_id'=>$request->deleted_by,'receiver_deleted'=>0])->first()){

            ChatMessage::where(['id'=>$request->message_id])->update(['receiver_deleted'=>1]);
            return sendSuccess('Message deleted successfully!',null);

        }else{

            return sendError('You are not allowed to delete this message!', null);
        }

    }
    public function deleteChat(Request $request){

        $validator = Validator::make($request->all(), [
            'deleted_by' => 'required|numeric|min:0|not_in:0',
            'chat_id' => 'required|numeric|min:0|not_in:0',
        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        $messages =  Chat::where('id',$request->chat_id)->first();

        if(!$messages){

            return sendError('No chat found!', null);
        }

        if (Chat::where(['id'=>$request->chat_id,'sender_id'=>$request->deleted_by,'sender_deleted'=>0])->first()){

            Chat::where(['id'=>$request->chat_id])->update(['sender_deleted'=>1]);
            return sendSuccess('Chat deleted successfully!',null);

        }elseif (Chat::where(['id'=>$request->chat_id,'receiver_id'=>$request->deleted_by,'receiver_deleted'=>0])->first()){

            Chat::where(['id'=>$request->chat_id])->update(['receiver_deleted'=>1]);
            return sendSuccess('Chat deleted successfully!',null);

        }else{

            return sendError('You are not allowed to delete this message!', null);
        }

    }
    public function chatGallery(Request $request){

        $validator = Validator::make($request->all(), [
            'chat_id' => 'required|numeric|min:0|not_in:0',
        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }

        $messages =  Chat::where('id',$request->chat_id)->first();

        if(!$messages){

            return sendError('No chat found!', null);
        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;

        $gallery = ChatMessage::where(['chat_id'=>$request->chat_id,'type'=>'media'])->whereNotNull('path')->orderBy('created_at','desc')->paginate($per_page);

        return sendSuccess('Chat gallery',$gallery);

    }
    public function chatSearch(Request $request){

        $validator = Validator::make($request->all(), [
            'search_text' => 'required|string',
        ]);
        if($validator->fails()){
            return sendError('The given data is invalid', $validator->getMessageBag());
        }
        $text = $request->search_text;

        $ids = User::Where('first_name', 'like', '%' . $text . '%')->orWhere('last_name', 'like', '%' . $text . '%')->distinct('id')->pluck('id');

        if(!$ids){

            return sendSuccess('No result found!', null);
        }
        $per_page = isset($request->per_page) ? $request->per_page : 20;

        $chats = Chat::with('sender','sender.stance_id', 'receiver','receiver.stance_id','lastMessage')->where(function ($q) use ($ids) {
            $q->where('sender_id', Auth::id());
            $q->orWhere('receiver_id', Auth::id());
        })->where(function ($q) use ($ids) {
            $q->whereIn('sender_id', $ids);
            $q->orWhereIn('receiver_id', $ids);
        })->paginate($per_page);


        return sendSuccess('Search Result!',$chats);

    }

    public function getCurrentChats(Request $request)
    {
//        $validator = Validator::make($request->all(), [
//            'search_text' => 'required|string',
//        ]);
//        if ($validator->fails()) {
//            return sendError('The given data is invalid', $validator->getMessageBag());
//        }
        $text = isset($request->search_text)?$request->search_text:"";
        $per_page = isset($request->per_page) ? $request->per_page : 20;
        $user_id = Auth::id();
        if (!empty($text)) {
            $ids = User::Where('first_name', 'like', '%' . $text . '%')->orWhere('last_name', 'like', '%' . $text . '%')->distinct('id')->pluck('id');

            if (!$ids) {

                return sendSuccess('No result found!', null);
            }
            $per_page = isset($request->per_page) ? $request->per_page : 20;

            $chats = Chat::with('sender', 'sender.stance_id', 'receiver', 'receiver.stance_id', 'lastMessage')->where(function ($q) use ($ids) {
                $q->where('sender_id', Auth::id());
                $q->orWhere('receiver_id', Auth::id());
            })->where(function ($q) use ($ids) {
                $q->whereIn('sender_id', $ids);
                $q->orWhereIn('receiver_id', $ids);
            })->paginate($per_page);


            return sendSuccess('Search Result!', $chats);
        } else {

            if (!$request->type || $request->type == 'current') {
                $chats = Chat::with('sender', 'sender.stance_id', 'receiver', 'receiver.stance_id', 'lastMessage')
                    ->withCount(['messages' => function ($q) {
                        $q->where('is_read', 0);
                    }])
                    ->where(function ($q) use ($user_id) {
                        $q->where('sender_id', $user_id);
                        $q->orWhere('receiver_id', $user_id);
                    })
                    ->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0")
                    ->orderBy('updated_at', 'desc')
                    ->paginate($per_page);
                return sendSuccess('Got chat successfully.', $chats);
            } else if ($request->type == 'past') {

                $chats = Chat::with('sender', 'sender.stance_id', 'receiver', 'receiver.stance_id', 'lastMessage')
                    ->withCount(['messages' => function ($q) {
                        $q->where('is_read', 0);
                    }])
                    ->where(function ($q) use ($user_id) {
                        $q->where('sender_id', $user_id);
                        $q->orWhere('receiver_id', $user_id);
                    })
                    ->whereRaw("IF(`sender_id` = $user_id, `sender_deleted`, `receiver_deleted`)= 0")
                    ->orderBy('updated_at', 'desc')
                    ->paginate($per_page);
                return sendSuccess('Got chat successfully.', $chats);
            } else {
                return sendError("Invalid type only allowed 'current' or 'past' ", null);
            }


        }
    }

}
