// thanks [YOU MIGHT NOT NEED JQUERY](http://youmightnotneedjquery.com/)
if (document.getElementById('blog_list'))
ready(function(){

    function hasClass(el,className)
    {
        if (el.classList)
        {
            return el.classList.contains(className);
        }
        else
        {
            return new RegExp('(^| )' + className + '( |$)', 'gi').test(el.className);
        }
    }

    function addClass(el,className)
    {
        if (el.classList)
            el.classList.add(className);
        else
            el.className += ' ' + className;
    }

    function removeClass(el,className)
    {
        if (el.classList)
            el.classList.remove(className);
        else
            el.className = el.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
    }

    function closest(el,className)
    {
        var tNode = el;
        do{
            if (hasClass(tNode,className))
            {
                break;
            }
            if (!tNode.parentNode)
            {
                return null;
            }
            tNode = tNode.parentNode;
        }
        while (tNode);
        return tNode;
    }

    function forEach(array, fn)
    {
        for (var i = 0; i < array.length; i++)
        fn(i,array[i]);
    }

    function offset(el)
    {
        var rect = el.getBoundingClientRect();

        return {
            top: rect.top + document.body.scrollTop+ document.documentElement.scrollTop,
            left: rect.left + document.body.scrollLeft+ document.documentElement.scrollLeft
        }
    }

    function fadeIn(el) {
        var opacity = 0;

        el.style.opacity = 0;
        el.style.filter = '';
        el.style.display = 'block';

        var last = +new Date();
        var tick = function() {
            opacity += (new Date() - last) / 400;
            el.style.opacity = opacity;
            el.style.filter = 'alpha(opacity=' + (100 * opacity)|0 + ')';

            last = +new Date();

            if (opacity < 1) {
                (window.requestAnimationFrame && requestAnimationFrame(tick)) || setTimeout(tick, 16);
            }
        };

        tick();
    }

    function getLink(link,callback)
    {
        var request = new XMLHttpRequest();
        request.open('GET', link, true);

        request.onreadystatechange = function() {
            if (this.readyState === 4) {
            if (this.status >= 200 && this.status < 400) {
                // Success!
                callback(this.responseText);
            } else {
                // Error :(
            }
            }
        };

        request.send();
        request = null;
    }

    /*加载更多*/
    function btn_loadmore(el,e){
        if (hasClass(el,'loading'))
        {
            return false;
        }
        addClass(el,'loading');
        el.innerHTML = '加载中...';

        var link = el.getAttribute('href');
        getLink(link+(link.indexOf('?')>0?'&':'?')+'is_ajax=1',function(response){
            el.style.display = 'none';
            if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', link]);}
            el.parentNode.removeChild(el);
            document.getElementById('blog_list').insertAdjacentHTML('beforeend', response);
        });
        e.preventDefault();e.stopPropagation();
        return false;
    };


    var isPjaxEnable = window.history && window.history.pushState && window.history.replaceState &&
            // pushState isn't reliable on iOS until 5.
            !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]\D|WebApps\/.+CFNetwork)/);

    // 列表页数据
    var linkThisPage = window.location.href;
    var titleThisPage = document.title;

    var state = {
            id:(new Date).getTime(),
            url: window.location.href,
            title: document.title,
            isDetail: 0,
        };
    window.history.replaceState(state, state.title, state.url);
    window.onpopstate = function(event) {
        if (event.state && event.state.id)
        {
            if (event.state.isDetail)
            {
                var el = document.getElementById(''+event.state.id);
                if (el)
                {
                    itemShow(el,0);
                }
                else
                {
                    window.history.go(0);
                }
            }
            else
            {
                itemHide();
            }
            if (event.state.title)
            {
                document.title = event.state.title;
            }
        }
        return false;
    };

    var _position,_scrollTop;

    function itemShow(itemNode,isPushState){
        var _this    = closest(itemNode,'item_bg');
        var _itemli  = closest(_this,'item_li');
        var _bodyli  = _this.querySelector('.item_body');
        var _content = _this.querySelector('.content');

        if (hasClass(_this,'item_bg_fixed'))
        {
            return false;
        }

        if (!_this.getAttribute('id'))
        {
            _this.setAttribute('id','item_'+(new Date).getTime());
        }
        // pjax 加载数据
        var state = {
            id      : _this.getAttribute('id'),
            url     : _this.querySelector('.name').getAttribute('href'),
            title   : _this.querySelector('.name').textContent || _this.querySelector('.name').innerText,
            isDetail: 1,
        };
        if (isPushState)
        {
            window.history.pushState(state, state.title, state.url);
            document.title = state.title;
        }
        if (!hasClass(_content,'content_load'))
        {
            getLink(state.url+(state.url.indexOf('?')>0?'&':'?')+'is_ajax=1',function(response){
                _content.innerHTML = response;
                addClass(_content,'content_load');
                forEach(_content.querySelectorAll('pre code'),function(i, block) {
                    hljs.highlightBlock(block);
                })
            });
        }

        // 记录当前位置（后面需要归位）
        var position = offset(_this);
        position.top = position.top - (document.body.scrollTop + document.documentElement.scrollTop);
        _position = position;

        // body禁止滚动
        _scrollTop = document.scrollingElement.scrollTop;
        var bodyWidth = document.body.offsetWidth;
        addClass(document.body,'body-prevent-class');
        document.body.style.top = -_scrollTop + 'px';
        document.body.style.width = bodyWidth+'px';

        // 瞬间切换到移动前位置（绝对定位后，移动到之前的位置，模拟成位置不变的样子）
        addClass(_this,'item_bg_fixed')
        addClass(_this,'item_showing');
        _bodyli.style.top    = position.top+'px';
        _bodyli.style.left   = position.left+'px';
        _bodyli.style.width  = _itemli.offsetWidth+'px';
        _bodyli.style.height = _itemli.offsetHeight+'px';
        _this.querySelector('.description').style.display = 'none';
        fadeIn(_this.querySelector('.content'));


        //开始移动
        anime.remove(_bodyli);
        anime({
            targets: _bodyli,
            scale: 1,
            top:0,
            left:offset(document.getElementById('blog_list')).left,
            width:document.getElementById('blog_list').offsetWidth+'px',
            height: [
                {
                    value:'256px',
                    duration:500*0.3,
                },
                {
                    value:'1000px',
                    duration:500*0.7,
                },
                {
                    value:'100pv',
                    duration:300,
                }
            ],
            // rotateY: '360deg',
            // rotate: 360,
            duration:800,
            complete: function(anim) {
                _bodyli.style.height = 'auto';
                removeClass(_this,'item_showing')
                if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
    }

    function itemHide(){
        var _this = document.querySelector('.item_bg_fixed');
        if (!_this)
        {
            return false;
        }
        var _itemli = closest(_this,'item_li');
        var _bodyli = _this.querySelector('.item_body');
        var _content = _this.querySelector('.content');

        // 准备移动
        addClass(_this,'item_hiding')
        _this.querySelector('.content').style.display='none';
        fadeIn(_this.querySelector('.description'));

        var position = _position;


        anime.remove(_bodyli);
        anime({
            targets: _bodyli,
            scale: 1,
            top   :(position.top)+'px',
            left  :(position.left)+'px',
            width :_itemli.offsetWidth+'px',
            height:_itemli.offsetHeight+'px',
            duration:500,
            complete: function(anim) {
                removeClass(_this,'item_bg_fixed');
                removeClass(_this,'item_hiding');
                _bodyli.style.top    = '';
                _bodyli.style.left   = '';
                _bodyli.style.width  = '';
                _bodyli.style.height = '';
                //  body 恢复滚动
                removeClass(document.body,'body-prevent-class')
                document.body.style.top    = '';
                document.body.style.width  = '';
                document.scrollingElement.scrollTop =  _scrollTop;
                if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
    }

    // Listen to all clicks on the document
    document.addEventListener('click', function (e) {

        var el = e.target;
        if (hasClass(el,'btn_loadmore'))
        {
            return btn_loadmore(el,e);
        }

        /*展开详情*/
        if (hasClass(el,'name') || closest(el,'description')  )
        {
            itemShow(el,1);
            e.preventDefault();e.stopPropagation();
            return false;
        }

        /*关闭详情*/
        if (hasClass(el,'btn_close'))
        {
            window.history.go(-1);
            e.preventDefault();e.stopPropagation();
            return false;
        }

        return ;

    }, false);

    return false;


});
