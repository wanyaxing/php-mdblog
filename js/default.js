function ready(fn) {
    if (document.readyState != 'loading'){
        fn();
    } else if (document.addEventListener) {
        document.addEventListener('DOMContentLoaded', fn);
    } else {
        document.attachEvent('onreadystatechange', function() {
            if (document.readyState != 'loading')
            fn();
        });
    }
}

ready(function(){
    document.getElementById('btn_sideshow').onclick = function(){
        var el = document.getElementById('side_list');
        if (el.classList) {
            el.classList.toggle('side_show');
        } else {
            var classes = el.className.split(' ');
            var existingIndex = -1;
            for (var i = classes.length; i--;) {
                if (classes[i] === 'side_show')
                    existingIndex = i;
            }

            if (existingIndex >= 0)
                classes.splice(existingIndex, 1);
            else
                classes.push('side_show');

            el.className = classes.join(' ');
        }
    };


    window.onpopstate = function(event) {
        window.history.go(0);
    }

    hljs.initHighlightingOnLoad();
});
