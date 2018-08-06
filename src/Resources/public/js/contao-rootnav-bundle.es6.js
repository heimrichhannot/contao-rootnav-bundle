(function(){
    let rootnav = {
        onReady : function(){
            document.querySelectorAll('.nav-select select').addEventListener('change', function(){
                window.location = this.querySelector().value;
            });
        }
    };
    if (document.attachEvent ? document.readyState === "complete" : document.readyState !== "loading"){
        rootnav.onReady();
    } else {
        document.addEventListener('DOMContentLoaded', rootnav.onReady());
    }
})();