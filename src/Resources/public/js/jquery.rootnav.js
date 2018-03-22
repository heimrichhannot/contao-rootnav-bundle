(function($){

    var rootnav = {
        onReady : function(){
            $('.nav-select select').on('change', function(){
                window.location = $(this).find('option:selected').val();
            });
        }
    }

    $(document).ready(function(){rootnav.onReady()});

})(jQuery);