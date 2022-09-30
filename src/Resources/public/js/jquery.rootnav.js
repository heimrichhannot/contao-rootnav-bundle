(function(){
    let rootnav = {
        onReady : function(){
            let rootnavs = document.querySelectorAll('.mod_huh_rootnav_module .nav-select select');

            if (rootnavs) {
                rootnavs.forEach(nav => {
                    nav.addEventListener('change', function(){
                        window.location = nav.value;
                    });
                });
            }
        }
    };
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
        rootnav.onReady();
    } else {
        document.addEventListener('DOMContentLoaded', rootnav.onReady());
    }
})();