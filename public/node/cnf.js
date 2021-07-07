var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);
// var db = require('./db.js');
// var mydb = new db();
const db = require('./db');
app.get('/', function (req, res) {
    res.send('CNF Working Fine');
});
var sockets = {};
var arr = [];
io.on('connection', function (socket) {

    socket.on('post_send', function (data) {
        io.emit('post_receive', {
            'post_id': data.post_id,
            'user_id': data.user_id,
            'user_name': data.user_name,
            'user_image': data.user_image,
            'description': data.description,
            'created_at': data.created_at,
            'medias' : data.medias
        });
    });

    socket.on('sparring_checkin_get', function (data) {
        io.emit('sparring_checkin_send', {
            'sender_id': data.sender_id,
            'receiver_id': data.receiver_id,
            'sparring_id': data.sparring_id,
            'checkin_id': data.checkin_id,
            'type': data.type,
            'status': data.status
        });
    });

    socket.on('like_send', function (data) {
        io.emit('like_receive', {
            'post_id': data.post_id,
            'user_id': data.user_id,
            'user_name': data.user_name,
            'user_image': data.user_image,
            'created_at': data.created_at
        });
    });

    socket.on('new_message_send', function (data) {
        io.emit('new_message_receive', {
            'chat_id': data.chat_id,
            'sender_id': data.sender_id,
            'receiver_id': data.receiver_id,
            'sender_name': data.sender_name,
            'sender_image': data.sender_image,
            'message': data.message,
            'type': data.type,
            'path': data.path,

        });
    });

    socket.on('notification_send', function (data) {
        io.emit('notification_receive', {
            'notification_id': data.notification_id,
            'sender_id': data.sender_id,
            'sender_name': data.sender_name,
            'sender_image': data.sender_image,
            'receiver_id': data.receiver_id,
            'type_id': data.type_id,
            'noti_type': data.noti_type,
            'noti_text': data.noti_text,
            'created_at': data.created_at
        });
    });

    socket.on('read_message_send', function (data) {
        io.emit('read_message_receive', {
            'chat_id': data.chat_id,
            'sender_id': data.sender_id,
            'receiver_id': data.receiver_id,
            'message_id': data.message_id,

        });
    });

    socket.on('counter_start_send', function (data) {
        io.emit('counter_start_receive', {
            'chat_id': data.chat_id,
            'other_user_id': data.other_user_id,
            'is_accept': data.is_accept,
            'start_match_user_id': data.start_match_user_id,
            'status': data.status,
        });
    });
    socket.on('decision_send', function (data) {
        io.emit('decision_receive', {
            'chat_id': data.chat_id,
            'sender_id': data.sender_id,
            'receiver_id': data.receiver_id,
            'decision': data.decision,
            'disputed_id':data.disputed_id,
            'disputed_chat_id':data.disputed_chat_id,
            'status':data.status,

        });
    });

    socket.on('revert_decision_send', function (data) {
        io.emit('revert_decision_receive', {
            'user_start': data.user_start,
            'competition_id': data.competition_id,
            'head_user_start': data.head_user_start,
        });
        new db().updateViewCount(data.user_start, data.competition_id, data.head_user_start);
    });

    socket.on('disconnect', function () {
        if (sockets[socket.id] != undefined) {
            mydb.releaseRequest(sockets[socket.id].user_id).then(function (result) {
                console.log('disconected: ' + sockets[socket.id].request_id);
                io.emit('request-released', {
                    'request_id': sockets[socket.id].request_id
                });
                delete sockets[socket.id];
            });
        }
    });
});


http.listen(5473, function () {
    console.log('5473 socket working fine');
});
