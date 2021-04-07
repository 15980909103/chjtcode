var ws = require("ws");
//var ws = require("nodejs-websocket");
var http = require('http');
var https = require('https');
var querystring = require('querystring');
var fs=require('fs');
var keypath=process.cwd()+'/chat.999house.com.key';//我把秘钥文件放在运行命令的目录下测试
var certpath=process.cwd()+'/chat.999house.com.crt';//console.log(keypath);
var mysql=require('mysql');
console.log("start connection...");
var DOMAINNAME="chfx.999house.com";
var config = require('./config.js');

var options = {
  key: fs.readFileSync(keypath),
  cert: fs.readFileSync(certpath)
  //passphrase:''//如果秘钥文件有密码的话，用这个属性设置密码
}

var conns={};

var server=https.createServer(options, function (req, res) {
    res.writeHead(403);//403即可
    res.end("This is a  WebSockets server!\n");
}).listen(1201);

server.on('error',function(conn){
	console.log(new Date()+" https server error"+'\n');
	setTimeout(() => {
		server=https.createServer(options, function (req, res) {
			res.writeHead(403);//403即可
			res.end("This is a  WebSockets server!\n");
	}).listen(1201);
		wss=new ws.Server({server:server});
	}, 1000);
});

var wss=new ws.Server({server:server});

wss.on('connection',function(conn){
  //console.log(JSON.stringify(conn)+'\n');
  var temp;
  var path; // 0:1小程序  2经纪人   1:经纪人id     2:小程序用户id
	var id=0;
	conn.on("close", function (code, reason) {  //关闭连接
		myClose(id);
	});
	conn.on("error", function (code, reason) {  //异常关闭
		
	});
	conn.on("message", function (str) {
		try{
			console.log(str+'\n');
			var arr=JSON.parse(str);
			if(arr['type']=='user'){
				if(arr['user']!=''){
					temp=myTrim(arr['user']);
					path=myValidation(conn,temp);
					if(path[0]=='1'){
						id=path[0]+'-'+path[2];
					}else{
						id=path[0]+'-'+path[1];
					}
					conns[id]=conn;
				}
			}else{
				if(arr['content']!=''){
					contentWxCheck(arr['content']).then(function(rs){
						//conn.send(JSON.stringify({'sender':'self','success':true,'data':arr['content']}));  //sender 发送方 self:经纪人自己  ta:客户
						if(!rs){
							conn.send(JSON.stringify({'sender':'self','success':false,'data':'发送失败'}));  //sender 发送方 self:经纪人自己  ta:客户
							return 
						}
						if(rs.errcode=='87014'){
							conn.send(JSON.stringify({'sender':'self','success':false,'data':'抱歉，您含有敏感信息不可提交'}));  //sender 发送方 self:经纪人自己  ta:客户
							return 
						}
	
						var client = mysql.createConnection(config.dbinfo);
						client.connect();
						var nowDate = Date.parse(new Date())/1000;
						if(path[0]=='1'){ //接受转发来自小程序客户的消息
							var toId='2-'+path[1];  //接收者id
							var sql="INSERT INTO 9h_xcx_chat_record (from_type,message_type,content,agent_id,user_id,agent_read,user_read,create_time,update_time) VALUES(?,?,?,?,?,?,?,?,?)";
							var sql_params=[path[0],arr['message_type'],arr['content'],path[1],path[2],'0','1',nowDate,nowDate];
						}else if(path[0]=='2'){  //接受转发来自经纪人的消息
							var toId='1-'+path[2];  //接收者id
							var sql="INSERT INTO 9h_xcx_chat_record (from_type,message_type,content,agent_id,user_id,agent_read,user_read,create_time,update_time) VALUES(?,?,?,?,?,?,?,?,?)";
							var sql_params=[path[0],arr['message_type'],arr['content'],path[1],path[2],'1','0',nowDate,nowDate];
						}
						client.query(sql,sql_params,function(err,res){
							if(err){  //数据库插入失败
								conn.send(JSON.stringify({'sender':'self','success':false,'data':arr['content']})); //sender 发送方 self:经纪人自己  ta:客户
								client.end();
								return;
							}
							//发送成功
							conn.send(JSON.stringify({'sender':'self','success':true,'data':arr['content']}));  //sender 发送方 self:经纪人自己  ta:客户
							//添加消息列表
							var selectSql="SELECT * FROM 9h_xcx_chat_list WHERE agent_id='"+path[1]+"' AND user_id='"+path[2]+"'";
							client.query(selectSql,function(err2,res2){
								if(err){return;}
								if(JSON.stringify(res2)==='[]'){  //添加数据操作
									var addSql="INSERT INTO 9h_xcx_chat_list (agent_id,user_id,create_time,update_time) VALUES(?,?,?,?)";
									var addSql_params=[path[1],path[2],nowDate,nowDate];
									client.query(addSql,addSql_params,function(err,res){});
									client.end();
								}else{  //修改数据操作
									var updateSql="UPDATE 9h_xcx_chat_list SET agent_status=?,user_status=?,update_time=? WHERE id=?";
									var updateSql_params=['1','1',nowDate,res2[0].id];
									client.query(updateSql,updateSql_params,function(err3,res3){});
									client.end();
								}
							})
							//查询接收者是否在线
							if(conns[toId]==undefined){ //对方不在线
		
							}else{  //对方在线
								conns[toId].send(JSON.stringify({'sender':'ta','success':true,'data':arr['content']}));  //sender 发送方 self:经纪人自己  ta:客户
							}
							//client.end();
						})
					}).catch(function(){
						conn.send(JSON.stringify({'sender':'self','success':false,'data':'发送失败'}));  //sender 发送方 self:经纪人自己  ta:客户
						return 
					})
				}
			}
		}catch(err){
			console.log(new Date()+" 01 "+err);
			//client.end();
		}
	})
});
console.log("WebSocket OK") 
function myTrim(x) {
    return x.replace(/\//g,'');
}
//关闭移除连接池
function myClose(_id){
  for (var index in conns){
    if(index==_id){
      delete conns[index];
    }
  }
  // radioOnlineNum();
}

function contentWxCheck(post_data){
	return new Promise(function(resolve,reject){
		try{
			post_data = querystring.stringify({
				'content':post_data,
				'dotype': 'post'
			});
			var post_options = {
				method: "POST",
				host: DOMAINNAME,
				port: 80,
				path: "/xcxapi/userAjax/contentWxCheck",
				headers: {
					"Content-Type":"application/x-www-form-urlencoded",
					"Content-Length": Buffer.byteLength(post_data)
				}
			};
			var post_req = http.request(post_options, function(res) {
				res.setEncoding('utf8');
				res.on('data', function (response) {
					response = JSON.parse(response)
					if(response.data){response.data=JSON.parse(response.data)}
					resolve(response.data)
				})
			})
			post_req.write(post_data);
			post_req.end();
		}catch(err){
			reject()
			console.log(new Date()+" "+err);
		}
	})
}

/**
 * 验证连接是否允许
 * [myValidation description]
 * @param  {[type]} myConn [description]
 * @param  {[type]} myPath [description]
 * @return {[type]}        [description]
 */
function myValidation (myConn,myPath) {
   try{
      myPath=myPath.split("?");
      if(myPath.length!=2){myConn.close(1001,'参数有误');return false;}   //验证条件是否满足
      var path=myPath[0];
      path=path.split("-");
      if(path.length!=3){myConn.close(1002,'部分参数有误');return false;}   //验证条件是否满足
      var token=myPath[1];
      if(token==""){myConn.close(3001,'token为空');return false;}   //验证条件是否满足
      //发送一个请求
      var post_data = querystring.stringify({
        'myPath':token,
      });
      var post_options = {
          method: "POST",
          host: DOMAINNAME,
          port: 80,
          path: "/xcxapi/userAjax/tokenValidation",
          headers: {
              "Content-Type":"application/x-www-form-urlencoded",
              "Content-Length": Buffer.byteLength(post_data)
          }
      };
      var post_req = http.request(post_options, function(res) {
        res.setEncoding('utf8');
        res.on('data', function (chunk) {
            chunk=JSON.parse(chunk);
            if(chunk.success){
              if(chunk.data.from_type!=path[0]){
                myConn.close(1003,'from_type不对应');return false;
              }else{
                if(chunk.data.from_type=='1'){
                  if(chunk.data.user_id!=path[2]){myConn.close(3002,'用户id不对应');return false;}
                }else{
                  if(chunk.data.agent_id!=path[1]){myConn.close(3003,'用户id不对应');return false;}
                }
              }
            }else{
              myConn.close(1008,chunk.msg);return false;
            }
        });
      }).on('error', function(e) {
          myConn.close(3001,'请求token验证失败');return false;
      });
      post_req.write(post_data);
      post_req.end();
      return path;
    }catch(err) {
      console.log(new Date()+" "+err);
	  return myValidation(myConn,myPath);
    }
}
//广播在线人id
/*function radioOnlineNum(){
  var onlineIds=Object.keys(conns);//array
   for (var i in conns){
      conns[i].send(JSON.stringify({'type':'chat','action':'online','data':onlineIds}))
  }
}*/