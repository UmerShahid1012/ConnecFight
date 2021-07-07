/**
 * Created by nomantufail on 10/27/2016.
 */
var db = function (){
    var self = this;
    var config = {
        host     : 'localhost',
        user     : 'root',
        password : 'LP)mUJQc*,?4~@xd',
        database: 'checkup'
    };
    self.mysql = function () {
        return require('mysql');
    };
    self.editing = function (requestId){
        return new Promise(
            // The resolver function is called with the ability to resolve or
            // reject the promise
            function(resolve, reject) {
                var connection = self.mysql().createConnection(config);
                connection.connect();
                connection.query("select editing from cust_vehicle_srv_reqs where id='"+requestId+"'" , function(err, rows, fields) {
                    connection.end();
                    if (err) reject(err);
                    resolve(rows[0].editing);
                });
            }
        );
    };
    self.lockRequest = function (userId, requestId){
        return new Promise(
            function(resolve, reject) {
                var connection = self.mysql().createConnection(config);
                connection.connect();
                connection.query("UPDATE cust_vehicle_srv_reqs SET editing='"+userId+"' WHERE id="+requestId , function(err, rows, fields) {
                    connection.end();
                    if (err) reject(err);
                    resolve(true);
                });
            }
        );
    };
    // this.updateViewCount = function (user_start, competition_id,head_user_start) {
    //
    //     return new Promise(
    //         function(resolve, reject) {
    //             var connection = self.mysql().createConnection(config);
    //             connection.connect();
    //             // UPDATE competitions
    //             // SET user_start = 0
    //             // WHERE id = 41
    //             if(user_start == 1){
    //                 connection.query("UPDATE" + " `competitions` SET `user_start` = '" 0 "' WHERE `competitions`.`id` = '" + competition_id + "';" , function(err, rows, fields) {
    //                     connection.end();
    //                     if (err) reject(err);
    //                     resolve(true);
    //                 });
    //             }else if(head_user_start == 1){
    //                 connection.query("UPDATE" + " `competitions` SET `head_user_status` = '" 0 "' WHERE `competitions`.`id` = '" + competition_id + "';" , function(err, rows, fields) {
    //                     connection.end();
    //                     if (err) reject(err);
    //                     resolve(true);
    //                 });
    //             }
    //
    //
    //         }
    //     );
    // };

    self.releaseRequest = function (userId){
        return new Promise(
            function(resolve, reject) {
                var connection = self.mysql().createConnection(config);
                connection.connect();
                connection.query("UPDATE cust_vehicle_srv_reqs SET editing='0' WHERE editing="+userId , function(err, rows, fields) {
                    connection.end();
                    if (err) reject(err);
                    resolve(true);
                });
            }
        );
    };
};
module.exports = db;
