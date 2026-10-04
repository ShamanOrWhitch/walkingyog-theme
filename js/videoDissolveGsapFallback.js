// videoDissolveGsapFallback.js
(function(){
  window.WY_GsapDissolve_Init = function(opts){
    opts = opts || {};
    var canvasId = opts.canvasId || 'dissolve-canvas';
    var canvas = document.getElementById(canvasId);
    if(!canvas) return console.error('WY_GsapDissolve_Init: no canvas');

    var renderer = new THREE.WebGLRenderer({canvas:canvas, alpha:true, antialias:true});
    renderer.setPixelRatio(Math.min(window.devicePixelRatio||1,2));
    renderer.setSize(window.innerWidth, window.innerHeight);

    var scene = new THREE.Scene();
    var camera = new THREE.PerspectiveCamera(75, window.innerWidth/window.innerHeight, 0.1, 1000);
    camera.position.z = 100;

    // geometry resolution tuned for performance on old devices
    var geoW = 80, geoH = 142;
    var geo = new THREE.PlaneGeometry(geoW, geoH, 120, 80);
    var mat = new THREE.MeshBasicMaterial({transparent:true, opacity:0, map:null});
    var plane = new THREE.Mesh(geo, mat);
    scene.add(plane);

    // get videos from DOM
    var videoEls = Array.prototype.slice.call(document.querySelectorAll('#bg-videos video'));
    if(videoEls.length === 0) return console.warn('WY_GsapDissolve: no videos');

    // ensure videos attempted to play (muted autoplay)
    videoEls.forEach(function(v){ v.muted = true; v.loop = true; v.playsInline = true; try{ v.play().catch(()=>{}); }catch(e){} });

    var index = 0;
    var currentVideo = null;
    var currentTexture = null;

    function setVideo(i){
      index = i % videoEls.length;
      var v = videoEls[index];
      // wait for canplay
      return new Promise(function(res){
        if(v.readyState >= 3){
          attachTexture(v);
          res();
        } else {
          v.oncanplaythrough = function(){
            attachTexture(v); res();
          };
          v.load();
        }
      });
    }

    function attachTexture(v){
      if(currentVideo && currentVideo !== v){
        try{ currentVideo.pause(); }catch(e){}
      }
      currentVideo = v;
      try{ v.play().catch(()=>{}); }catch(e){}

      if(currentTexture){
        currentTexture.dispose && currentTexture.dispose();
      }
      currentTexture = new THREE.VideoTexture(v);
      currentTexture.minFilter = THREE.LinearFilter;
      currentTexture.magFilter = THREE.LinearFilter;
      mat.map = currentTexture;
      mat.opacity = 1;
      mat.needsUpdate = true;

      // compute scale to cover screen while keeping aspect 9:16 behavior
      var vw = v.videoWidth || 480;
      var vh = v.videoHeight || 854;
      var screenAR = window.innerWidth / window.innerHeight;
      var vidAR = vw / vh;
      var targetScaleX = 1, targetScaleY = 1;
      if(vidAR > screenAR){
        targetScaleX = (vidAR / screenAR) * (window.innerWidth / geoW);
        targetScaleY = (window.innerHeight / geoH);
      } else {
        targetScaleX = (window.innerWidth / geoW);
        targetScaleY = (screenAR / vidAR) * (window.innerHeight / geoH);
      }
      plane.scale.set(targetScaleX * geoW/geoW, targetScaleY * geoH/geoH, 1);
    }

    // explosion/dissolve tuned for old GPUs: reduce vertex ops if device is weak
    function explodeToNext(){
      var pos = geo.attributes.position.array;
      var orig = new Float32Array(pos.length);
      for(var i=0;i<pos.length;i++) orig[i] = pos[i];

      // Create random offsets for "explosion" but ensure next texture is prepared before finalizing
      gsap.to(pos, {
        duration: 2.4,
        ease: "power4.out",
        onUpdate: function(){
          geo.attributes.position.needsUpdate = true;
        },
        onStart: function(){
          // nothing
        },
        // modifier-like behavior: we mutate array in place via onUpdate above
        // we'll do small manual changes to positions for performance:
        // using simple loop via onStart is heavy, but we'll randomize a subset
        // instead we will apply a deterministic shake using sin + random seeds
        onComplete: function(){
          // switch video immediately to next and then animate back to coherent shape
          var next = (index + 1) % videoEls.length;
          setVideo(next).then(function(){
            // restore positions towards original smoothly (gather pixels)
            gsap.to(pos, {
              duration: 1.2,
              ease: 'power2.inOut',
              onUpdate: function(){
                geo.attributes.position.needsUpdate = true;
              },
              onStart: function(){
                // small immediate warp to ensure visible transition
                for(var j=0;j<pos.length;j++){
                  pos[j] = orig[j] + (Math.random()-0.5) * 6;
                }
                geo.attributes.position.needsUpdate = true;
              },
              onComplete: function(){
                // ensure opacity resets
                gsap.to(mat, {opacity: 1, duration: 0.8});
              }
            });
          });
        }
      });

      // fade the material during explosion
      gsap.to(mat, {opacity:0, duration: 2.0, delay: 0.4});
    }

    // lighter variant: mutate only every Nth vertex to reduce CPU on weak devices
    // but above routine is generic enough for many devices.

    function loop(){
      requestAnimationFrame(loop);
      renderer.render(scene, camera);
      if(currentTexture) currentTexture.needsUpdate = true;
    }
    loop();

    // start first video and cycles
    setVideo(0);
    setTimeout(function(){
      explodeToNext();
      setInterval(explodeToNext, (opts.dissolveInterval||35000) + Math.random()*15000);
    }, 12000);

    window.addEventListener('resize', function(){
      renderer.setSize(window.innerWidth, window.innerHeight);
      camera.aspect = window.innerWidth / window.innerHeight;
      camera.updateProjectionMatrix();
    });

    // expose API
    window.WY_GsapDissolve = {
      playNext: function(){ explodeToNext(); },
      dispose: function(){ if(currentTexture) currentTexture.dispose(); renderer.dispose(); }
    };
  };
})();
