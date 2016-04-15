var canvas,
	gl,
	squareVerticesBuffer,
	squareVerticesColorBuffer,
	mvMatrix,
	shaderProgram,
	vertexPositionAttribute,
	vertexColorAttribute,
	perspectiveMatrix;

var colors = new Float32Array([
    0.9,  0.7,  0.6,  0.5,
    0.9,  0.6,  0.5,  0.5,
    0.9,  0.6,  0.6,  0.5,
    0.9,  0.6,  0.5,  0.5
]);
  
var targetColors = new Float32Array([
    0.9,  0.7,  0.1,  0.5,
    0.5,  0.2,  0.2,  0.5,
    0.4,  0.8,  0.4,  0.5,
    0.6,  0.2,  0.2,  0.5
]);

var bgColor;
var bgColorWhite = new Array("#F7FAFA", "#F9FDFA", "#FBFAED", "#F3FBFB", "#FBFAF8", "FDF6F3", "#F8F0F0", "#FAFAFA", "#EFF3F2", "#FAFAFB","#FFFFFF");
var bgColorSpecial = new Array("#273d3d", "#7B7526", "#114349", "#78694f", "#89695f", "#9e6868", "#303627", "#2F4B44", "#07334D", "#43514A", "#222224");

function start() {
	console.log("index.js, setup start");

initAudioFiles();

  nightCheck();
  setInterval(nightCheck, 1000*60);

  canvas = document.getElementById("glcanvas");
  initWebGL(canvas);
    
  if (gl) {
    gl.clearColor(1.0, 1.0, 1.0, 1.0);
    gl.clearDepth(1.0);
    gl.disable(gl.DEPTH_TEST);
/*     gl.depthFunc(gl.LEQUAL); */
    initShaders();
    initBuffers();    
    setInterval(drawScene, 15);
  }else{
  	$('#glcanvas').css("background-color","#9e6868");
  }

  $('html, body').css("background-color","white");

  	// main rect
   changeColor();
   setInterval(changeColor, 9200);
   setInterval(changeColor, 16000);
   setInterval(changeColor, 17200);

   // background
  changeBackgoundColorRandom();
  setInterval(changeBackgoundColorRandom, 10000);

  // snd
/*
  playLoopById('gsnd1');
  playLoopById('test');
*/

	setVolumeById('gsnd1', 0.05);
/* 	setVolumeById('test', 0.01); */

	playEchoById('gsnd1', 140, 3000);
	playEchoById('gsnd1', 150, 3000);
	playEchoById('gsnd1', 160, 3000);

/*  	playEchoById('test', 5000, 1000); */

	setTimeout(function(){randomSound();}, 5000);
	setTimeout(function(){randomSound();}, 8000);
	setTimeout(function(){randomSound();}, 10000);
}

function randomSound(){
	var sndType = Math.ceil(Math.random() * 11);
	var time = 1000 + Math.floor(Math.random() * 30000);
	playById('test'+sndType.toString());
	setTimeout(function(){ randomSound();}, time);
}


var randomNight = 1;
var night;

function nightCheck(){

	if(randomNight==0){
	    var now = new Date();
	    var hour = now.getHours();
	    if(21 <= hour || hour <= 3){
		    night = 1;
	    }else{
		    night = 0;
		}
	}else{
		//night = Math.random()*10 > 5.0;
		night = 0;
	}
}

// css color animate
function changeBackgoundColorRandom(){
	if(night){
		bgColor = bgColorSpecial;
	}else{
		bgColor = bgColorWhite;
	}
	var colorChoice = Math.round(Math.random()*10.0);
	var time = Math.random()*10 + 20;
	$('html, body').animate( { 'background-color': bgColor[colorChoice] }, time);
	if(gl)
	 	$('#glcanvas').animate( { 'background-color': bgColor[colorChoice] }, time);
}


var animationSpeed = 0.01;
var rgb = 0;
var counter = 0;
var randomFactor = 0;

function changeColor(){
	if(gl){
		counter++;
		if(counter>10){
			counter = 0;
			randomFactor = Math.random();
			if(randomFactor>0.5){
				rbg = 3;
				if(randomFactor>0.9){
					rgb = 2;
				}
			}else{
				rgb = 0;
			}
		}
		
		for(var i=0; i<16; i++){		
			if((i+1)%4==0){
				// alpha
				targetColors[i] = 1.0;// + Math.random()*0.2;
			}else{
				if((i+rgb)%4==0){
					// rgb
					targetColors[i] = 0.9 + Math.random()*0.1;
				}else{
					targetColors[i] = Math.random();
				}
				if(night==1){
					targetColors[i] *= 0.4;
				}
			}
		}
	}else{
		if(night){
			bgColor = bgColorSpecial;
		}else{
			bgColor = bgColorWhite;
			}
		var colorChoice = Math.round(Math.random()*10.0);
		var time = Math.random()*100 + 200;
		$('#glcanvas').animate( { 'background-color': bgColor[colorChoice] }, time);
	}
}

function initWebGL() {
  gl = null;
  
  try { gl = canvas.getContext("experimental-webgl");
  }catch(e) {
  }
  
  if(!gl) { alert("Unable to initialize WebGL. Turn on webGL if your browser support."); }
}
  

function initBuffers() {
  
  squareVerticesBuffer = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, squareVerticesBuffer);
  var vertices = [ 1.0,1.0,0.0, -1.0,1.0,0.0, 1.0,-1.0,0.0, -1.0,-1.0,0.0 ];
  
  gl.bufferData(gl.ARRAY_BUFFER, new Float32Array(vertices), gl.STATIC_DRAW);
  
  squareVerticesColorBuffer = gl.createBuffer();
  gl.bindBuffer(gl.ARRAY_BUFFER, squareVerticesColorBuffer);
  gl.bufferData(gl.ARRAY_BUFFER, colors, gl.DYNAMIC_DRAW);
}


function animateColor(){
	for(var i=0; i<16; i++){
		colors[i] *= 1000.0;
 		colors[i] = colors[i]*0.998 + targetColors[i]*1000.0*0.002;
 		colors[i] *= 0.001;
	}

  gl.bindBuffer(gl.ARRAY_BUFFER, squareVerticesColorBuffer);
  gl.bufferData(gl.ARRAY_BUFFER, colors, gl.DYNAMIC_DRAW);
}

function drawScene() {
	var w = gl.canvas.width;
	var h = gl.canvas.height;

	gl.clear(gl.COLOR_BUFFER_BIT | gl.DEPTH_BUFFER_BIT);
  
	animateColor();
      
  perspectiveMatrix = makeOrtho(-1, 1, -1, 1, 0.1, 100.0);

  loadIdentity();
  
   mvTranslate([0.0, 0.0, -0.1]);
  
  gl.bindBuffer(gl.ARRAY_BUFFER, squareVerticesBuffer);
  gl.vertexAttribPointer(vertexPositionAttribute, 3, gl.FLOAT, false, 0, 0);
  
  gl.bindBuffer(gl.ARRAY_BUFFER, squareVerticesColorBuffer);
  gl.vertexAttribPointer(vertexColorAttribute, 4, gl.FLOAT, false, 0, 0);
  
  setMatrixUniforms();
  gl.drawArrays(gl.TRIANGLE_STRIP, 0, 4);
}

function initShaders() {
  var fragmentShader = getShader(gl, "shader-fs");
  var vertexShader = getShader(gl, "shader-vs");

  shaderProgram = gl.createProgram();
  gl.attachShader(shaderProgram, vertexShader);
  gl.attachShader(shaderProgram, fragmentShader);
  gl.linkProgram(shaderProgram);
  
  if (!gl.getProgramParameter(shaderProgram, gl.LINK_STATUS)) {
    alert("Unable to initialize the shader program.");
  }
  
  gl.useProgram(shaderProgram);
  
  vertexPositionAttribute = gl.getAttribLocation(shaderProgram, "aVertexPosition");
  gl.enableVertexAttribArray(vertexPositionAttribute);
  
  vertexColorAttribute = gl.getAttribLocation(shaderProgram, "aVertexColor");
  gl.enableVertexAttribArray(vertexColorAttribute);
}


function getShader(gl, id) {
  var shaderScript = document.getElementById(id);
  
  if (!shaderScript) {
    return null;
  }
  
  var theSource = "";
  var currentChild = shaderScript.firstChild;
  
  while(currentChild) {
    if (currentChild.nodeType == 3) {
      theSource += currentChild.textContent;
    }
    
    currentChild = currentChild.nextSibling;
  }
  
  var shader;
  
  if (shaderScript.type == "x-shader/x-fragment") {
    shader = gl.createShader(gl.FRAGMENT_SHADER);
  } else if (shaderScript.type == "x-shader/x-vertex") {
    shader = gl.createShader(gl.VERTEX_SHADER);
  } else {
    return null;  // Unknown shader type
  }
    
  gl.shaderSource(shader, theSource);
  gl.compileShader(shader);
  
  if (!gl.getShaderParameter(shader, gl.COMPILE_STATUS)) {
    alert("An error occurred compiling the shaders: " + gl.getShaderInfoLog(shader));
    return null;
  }
  
  return shader;
}

//
// Matrix utility functions
//
function loadIdentity() {
  mvMatrix = Matrix.I(4);
}

function multMatrix(m) {
  mvMatrix = mvMatrix.x(m);
}

function mvTranslate(v) {
  multMatrix(Matrix.Translation($V([v[0], v[1], v[2]])).ensure4x4());
}

function setMatrixUniforms() {
  var pUniform = gl.getUniformLocation(shaderProgram, "uPMatrix");
  gl.uniformMatrix4fv(pUniform, false, new Float32Array(perspectiveMatrix.flatten()));

  var mvUniform = gl.getUniformLocation(shaderProgram, "uMVMatrix");
  gl.uniformMatrix4fv(mvUniform, false, new Float32Array(mvMatrix.flatten()));
}


