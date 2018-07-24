$(function(){
    $('#btn_sideshow').click(function(){
        $('#side_list').toggleClass('side_show');
    });

    window.onpopstate = function(event) {
        window.history.go(0);
    }

    hljs.initHighlightingOnLoad();
});

