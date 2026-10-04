(function(){
    // feature checks
    function supportsWebGL2(){
        try {
            var c = document.createElement('canvas');
            return !!(c.getContext && c.getContext('webgl2'));
        } catch(e){
            return false;
        }
    }
    function supportsFloatTextures(){
        try {
            var c = document.createElement('canvas');
            var gl = c.getContext('webgl') || c.getContext('experimental-webgl');
            if(!gl) return false;
            return !!(gl.getExtension && (gl.getExtension('OES_texture_float') || gl.getExtension('OES_texture_half_float')));
        } catch(e){
            return false;
        }
    }

    // export global starter
    window.WY_DissolveManager_Start = function(opts){
        opts = opts || {};
        var themeUrl = (window.wy_theme_url || (window.wy_theme_url = (function(){
            try { return window.wy_theme_url; } catch(e){ return ''; }
        })())) || '';

        // get video list from DOM
        var videoNodes = Array.prototype.slice.call(document.querySelectorAll('#bg-videos video'));
        if(videoNodes.length === 0){
            console.warn('WY_DissolveManager: no videos found in #bg-videos');
            return;
        }
        // expose list for fallbacks
        window.wyVideoList = videoNodes.map(function(v){ return v.src || v.getAttribute('src'); });

        // decide shader capability
        var canShader = supportsWebGL2() && supportsFloatTextures();
        console.log('WY_DissolveManager: canShader=', canShader);

        if(canShader){
            // try load shader script
            var s = document.createElement('script');
            s.src = themeUrl + '/js/videoDissolveShader.js';
            s.onload = function(){
                console.log('WY_DissolveManager: shader loaded');
                if(window.WY_ShaderDissolve_Init) window.WY_ShaderDissolve_Init(opts);
                else {
                    console.warn('WY_DissolveManager: WY_ShaderDissolve_Init not found — loading fallback');
                    loadFallback();
                }
            };
            s.onerror = function(){
                console.warn('WY_DissolveManager: shader failed to load — fallback');
                loadFallback();
            };
            document.body.appendChild(s);
        } else {
            // fallback immediately
            loadFallback();
        }

        function loadFallback(){
            var f = document.createElement('script');
            f.src = themeUrl + '/js/videoDissolveGsapFallback.js';
            f.onload = function(){
                console.log('WY_DissolveManager: fallback loaded');
                if(window.WY_GsapDissolve_Init) window.WY_GsapDissolve_Init(opts);
                else console.error('WY_DissolveManager: WY_GsapDissolve_Init not found');
            };
            f.onerror = function(){
                console.error('WY_DissolveManager: failed to load fallback');
            };
            document.body.appendChild(f);
        }
    };
})();
