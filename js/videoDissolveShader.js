// videoDissolveShader.js
(function(){
  function createNoiseTexture(size = 256){
    var canvas = document.createElement('canvas');
    canvas.width = canvas.height = size;
    var ctx = canvas.getContext('2d');
    var id = ctx.createImageData(size, size);
    for(var i=0;i<size*size;i++){
      var v = Math.floor(Math.random()*255);
      id.data[i*4] = id.data[i*4+1] = id.data[i*4+2] = v;
      id.data[i*4+3] = 255;
    }
    ctx.putImageData(id,0,0);
    var tex = new THREE.Texture(canvas);
    tex.needsUpdate = true;
    tex.wrapS = tex.wrapT = THREE.RepeatWrapping;
    return tex;
  }

  var DEFAULTS = {
    canvasId: 'dissolve-canvas',
    dissolveInterval: 35000,
    autoPlay: true
  };

  window.WY_ShaderDissolve_Init = function(opts){
    var o = Object.assign({}, DEFAULTS, opts || {});
    var canvas = document.getElementById(o.canvasId);
    if(!canvas){
      console.error('WY_ShaderDissolve_Init: canvas not found');
      return;
    }

    var rect = canvas.getBoundingClientRect();
    function resizeCanvasToDisplaySize(){
      var w = window.innerWidth;
      var h = window.innerHeight;
      canvas.width = w;
      canvas.height = h;
    }
    resizeCanvasToDisplaySize();

    var renderer = new THREE.WebGLRenderer({canvas:canvas, alpha:true, antialias:true});
    renderer.setPixelRatio(Math.min(window.devicePixelRatio||1, 2));
    renderer.setSize(window.innerWidth, window.innerHeight, false);

    var scene = new THREE.Scene();
    var camera = new THREE.OrthographicCamera(-window.innerWidth/2, window.innerWidth/2, window.innerHeight/2, -window.innerHeight/2, -1000, 1000);
    camera.position.z = 1;

    var geometry = new THREE.PlaneBufferGeometry(window.innerWidth, window.innerHeight, 1, 1);

    var uniforms = {
      uTexture: { value: null },
      uNoise: { value: createNoiseTexture(256) },
      uProgress: { value: 0.0 },
      uTime: { value: 0.0 },
      uResolution: { value: new THREE.Vector2(window.innerWidth, window.innerHeight) },
      uNoiseScale: { value: 1.2 }
    };

    var frag = [
      'varying vec2 vUv;',
      'uniform sampler2D uTexture;',
      'uniform sampler2D uNoise;',
      'uniform float uProgress;',
      'uniform float uTime;',
      'uniform vec2 uResolution;',
      'uniform float uNoiseScale;',
      'void main(){',
        'vec2 uv = vUv;',
        'vec2 nuv = uv * uNoiseScale + vec2(uTime * 0.02);',
        'float noise = texture2D(uNoise, nuv).r;',
        'float threshold = smoothstep(uProgress - 0.2, uProgress + 0.2, noise);',
        'vec4 col = texture2D(uTexture, uv);',
        'col.a *= 1.0 - threshold;',
        'gl_FragColor = col;',
      '}'
    ].join('\n');

    var vert = [
      'varying vec2 vUv;',
      'void main(){',
      '  vUv = uv;',
      '  gl_Position = projectionMatrix * modelViewMatrix * vec4(position,1.0);',
      '}'
    ].join('\n');

    var material = new THREE.ShaderMaterial({
      uniforms: uniforms,
      fragmentShader: frag,
      vertexShader: vert,
      transparent: true
    });

    var mesh = new THREE.Mesh(geometry, material);
    scene.add(mesh);

    // video DOM nodes
    var videos = Array.prototype.slice.call(document.querySelectorAll('#bg-videos video'));
    if(videos.length === 0){ console.warn('WY_ShaderDissolve: no videos'); return; }

    var videoEl = null;
    var videoTexture = null;
    var idx = 0;

    async function setVideo(i){
      idx = i % videos.length;
      var v = videos[idx];

      // ensure preloading and play (muted allowed)
      try {
        if(v.readyState < 3) await new Promise(r => { v.oncanplaythrough = r; v.load(); });
        await v.play().catch(()=>{ /* autoplay blocked */ });
      } catch(e){ /* ignored */ }

      if(videoTexture){ videoTexture.dispose && videoTexture.dispose(); }
      videoTexture = new THREE.VideoTexture(v);
      videoTexture.minFilter = THREE.LinearFilter;
      videoTexture.magFilter = THREE.LinearFilter;
      videoTexture.format = THREE.RGBFormat;
      uniforms.uTexture.value = videoTexture;

      // adjust plane scale to video aspect keeping full cover
      var vw = v.videoWidth || (v.getAttribute('data-w')||window.innerWidth);
      var vh = v.videoHeight || (v.getAttribute('data-h')||window.innerHeight);
      if(vw && vh){
        var screenAR = window.innerWidth / window.innerHeight;
        var vidAR = vw / vh;
        var scaleX = 1, scaleY = 1;
        if(vidAR > screenAR){
          // video wider -> scaleY full, scaleX larger
          scaleX = vidAR / screenAR;
          scaleY = 1;
        } else {
          scaleX = 1;
          scaleY = screenAR / vidAR;
        }
        mesh.scale.set(scaleX * window.innerWidth, scaleY * window.innerHeight, 1);
      } else {
        mesh.scale.set(window.innerWidth, window.innerHeight, 1);
      }
    }

    // animation loop
    var clock = new THREE.Clock();
    function animate(){
      requestAnimationFrame(animate);
      uniforms.uTime.value += clock.getDelta();
      renderer.render(scene, camera);
      if(videoTexture) videoTexture.needsUpdate = true;
    }
    animate();

    // dissolve routine (GSAP controls uniform if available)
    function dissolveNow(){
      if(window.gsap){
        gsap.to(uniforms.uProgress, { value: 1, duration: 2.4, ease: "power4.out", onComplete: function(){
            var next = (idx + 1) % videos.length;
            setVideo(next).then(function(){
                gsap.to(uniforms.uProgress, { value: 0, duration: 0.9, ease: "power2.inOut" });
            });
        }});
      } else {
        // simple timeout fallback
        uniforms.uProgress.value = 1;
        setTimeout(function(){
          setVideo((idx+1)%videos.length);
          uniforms.uProgress.value = 0;
        }, 2400);
      }
    }

    // start behavior
    setVideo(0);
    setTimeout(function(){
      dissolveNow();
      setInterval(dissolveNow, o.dissolveInterval + Math.random()*20000);
    }, 12000);

    // resize handler
    window.addEventListener('resize', function(){
      renderer.setSize(window.innerWidth, window.innerHeight, false);
      camera.left = -window.innerWidth/2; camera.right = window.innerWidth/2;
      camera.top = window.innerHeight/2; camera.bottom = -window.innerHeight/2;
      camera.updateProjectionMatrix();
      uniforms.uResolution.value.set(window.innerWidth, window.innerHeight);
      mesh.geometry.dispose();
      mesh.geometry = new THREE.PlaneBufferGeometry(window.innerWidth, window.innerHeight, 1, 1);
    });

    // expose control
    window.WY_ShaderDissolve = {
      dispose: function(){
        if(videoTexture) videoTexture.dispose();
        renderer.dispose();
      }
    };
  };
})();
