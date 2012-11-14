
var http = require('http');
var url = require('url');
var events = require('events');
var emitter = new events.EventEmitter();


var server = http.createServer(function (request, response) {
    response.writeHead(200, {
        "Content-Type": "text/plain"
    });
    
    var url_parts = url.parse(request.url, true);
    var query = url_parts.query;
    console.log(query);
    var eventName = 'yiiEvents'+query.name;
    if (query.action == 'waitForEvent'){
        var callback = function(message) {
            response.end('{"status" : "true", "message":"'+message+'"}');
        };        
        
        //set event listenter
        emitter.once(eventName, callback);
        
        
        // timeout limits execution
        setTimeout(function(){
            emitter.removeListener(eventName, callback);
            response.end('{"status" : "false", "message":""}');
        }, query.timeout);
    }else if(query.action == 'emitEvent'){
        emitter.emit(eventName,query.message);
        response.end();
    }else{
        response.end("wtf?\n");
    }

});


server.listen(8000);

// Put a friendly message on the terminal
console.log("Server running at http://127.0.0.1:8000/");