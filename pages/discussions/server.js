
var http = require("http");
var fs = require("fs");

var server = http.createServer();

server.on("request", function(request, response) {

  fs.readFile("index.php", function(err, data) {

    if (err) throw err;

    response.writeHead(200, {
      "Content-type": "text/html; charset=utf-8"
    });
    response.end(data);

  })

});

server.listen(8080);
