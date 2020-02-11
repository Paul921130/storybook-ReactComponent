// LOAD MODULES =================================================================
var express    = require('express');
var serveIndex = require('serve-index');
var serveStatic = require('serve-static');
var path = require('path');
var app = express();
var router = express.Router();
var port = process.env.PORT || 2295;

// SETUP EXPRESS APPLICATION ====================================================
app.use('/', router);
app.use('/', serveIndex('./', {
	'icons': true,
	'view': 'details',
	'filter': function(filename, index, files, dir) {
		if ( filename == 'node_modules' || filename == 'server.js' || /\.ejs$/.test(filename) || filename == 'gruntfile.js' ) {
			return false;
		} else {
			return true;
		}
	}
}));

// app.use('/Content/F2E', serveIndex('./dist', {
// 	'icons': true,
// 	'view': 'details',
// 	'filter': function(filename, index, files, dir) {
// 		if ( filename == 'node_modules' || filename == 'server.js' || /\.ejs$/.test(filename) || filename == 'gruntfile.js' ) {
// 			return false;
// 		} else {
// 			return true;
// 		}
// 	}
// }));

// app.use('/Content/F2E', serveStatic(path.resolve(__dirname, './dist'), {
// 	'index': ['default.html', 'default.htm'],
// }));


app.use(serveStatic(__dirname, {
	'index': ['default.html', 'default.htm'],
}));

app.listen(port);

router.use('/',function(req,res,next){
	console.log('%s %s',req.method,req.url||req.path);
	next();
});

router.get('/Category/_page/Advertisement', function (req, res) {
	let returnData = [
		{
			type: 'top',
			data: {
				'imgPC': 'https://fakeimg.pl/1920x560/?text=Hello',
				'imgM': 'https://fakeimg.pl/360x55/?text=Hello',
				'href': 'www.liontravel.com',
				'blank': true,
				'attrs': {}
			}
		},
		{
			type: 'float',
			data: {
				'imgPC': 'https://fakeimg.pl/1190x150/?text=Hello',
				'imgM': 'https://fakeimg.pl/75x70/?text=Hello',
				'href': 'www.liontravel.com',
				'blank': true,
				'attrs': {}
			}
		},
		{
			type: 'logo',
			data: {
				'imgPC': './pic.jpg',
				'imgM': './pic.jpg',
				'href': 'www.liontravel.com',
				'blank': true,
				'attrs': {}
			}
		}
	];
	res.send(returnData);
});

