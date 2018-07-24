$(function(){
    /*加载更多*/
    $('#blog_list').on('click','.btn_loadmore',function(e){
        var $this= $(this);
        if ($this.hasClass('loading'))
        {
            return false;
        }
        $this.addClass('loading').html('加载中...');
        var link = $this.attr('href');
        $.get(link).then(function(response){
            $this.fadeOut(function(){
                if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', link]);}
                $this.remove();
                $('#blog_list').append(response);
            });
        });
        e.preventDefault();e.stopPropagation();
        return false;
    });


    var isPjaxEnable = window.history && window.history.pushState && window.history.replaceState &&
            // pushState isn't reliable on iOS until 5.
            !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]\D|WebApps\/.+CFNetwork)/);

    // 列表页数据
    var linkThisPage = window.location.href;
    var titleThisPage = $('title').text();

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
                var $itemNode = $('#'+event.state.id);
                if ($itemNode.length>0)
                {
                    itemShow($itemNode,0);
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

    function itemShow(itemNode,isPushState){
        var $this = $(itemNode).closest('.item_bg');
        var $itemli = $this.closest('.item_li');
        var $bodyli = $this.find('.item_body');
        var $content = $this.find('.content');

        if ($this.hasClass('item_bg_fixed'))
        {
            return false;
        }

        if (!$this.attr('id'))
        {
            $this.attr('id','item_'+(new Date).getTime());
        }
        // pjax 加载数据
        var state = {
            id:$this.attr('id'),
            url: $this.find('.name').attr('href'),
            title: $this.find('.name').text(),
            isDetail: 1,
        };
        if (isPushState)
        {
            window.history.pushState(state, state.title, state.url);
            $('title').html(state.title);
        }
        if (!$content.hasClass('content_load'))
        {
            $.get(state.url).then(function(response){
                $content.html(response).addClass('content_load');
            });
        }

        // 记录当前位置（后面需要归位）
        var position = $this.offset();
        position.top = position.top - (document.body.scrollTop + document.documentElement.scrollTop);
        $this.data('position',position);

        // body禁止滚动
        var scrollTop = document.scrollingElement.scrollTop;
        var bodyWidth = $('body').width();
        $('body').addClass('body-prevent-class').data('scrollTop',scrollTop).css({'top':-scrollTop + 'px','width':bodyWidth+'px'});

        // 瞬间切换到移动前位置（绝对定位后，移动到之前的位置，模拟成位置不变的样子）
        $this.addClass('item_bg_fixed').addClass('item_showing');
        $bodyli.css({'top':position.top+'px','left':position.left+'px','width':$itemli.width()+'px','height':$itemli.height()+'px'})
                .addClass('anime');
        $this.find('.description').hide();
        $this.find('.content').fadeIn();


        //开始移动
        var _this = $bodyli[0];
        anime.remove(_this);
        anime({
            targets: _this,
            scale: 1,
            top:0,
            left:$('#blog_list').offset().left,
            width:$('#blog_list').width()+'px',
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
                $bodyli.css('height','auto');
                $bodyli.removeClass('anime');
                $this.removeClass('item_showing')
                if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
    }

    function itemHide(){
        var $this = $('.item_bg_fixed');
        if (!$this.hasClass('item_bg_fixed'))
        {
            return false;
        }
        var $itemli = $this.closest('.item_li');
        var $bodyli = $this.find('.item_body');
        var $content = $this.find('.content');

        // 准备移动
        $this.addClass('item_hiding')
        $bodyli.addClass('anime');
        $this.find('.content').hide();
        $this.find('.description').fadeIn();
        $this.css({
                    });
        var position = $this.data('position');


        var _this = $bodyli[0];
        anime.remove(_this);
        anime({
            targets: _this,
            scale: 1,
            top:(position.top)+'px',
            left:(position.left)+'px',
            width:$itemli.width()+'px',
            height:$itemli.height()+'px',
            duration:500,
            complete: function(anim) {
                $this.removeClass('item_bg_fixed').removeClass('item_hiding');
                $bodyli.removeClass('anime');
                $bodyli.css({
                            'top':'',
                            'left':'',
                            'width':'',
                            'height':'',
                        });
                //  body 恢复滚动
                $('body').removeClass('body-prevent-class').css({'top':'','width':''});
                document.scrollingElement.scrollTop =  $('body').data('scrollTop');
                if (typeof _hmt != 'undefined'){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
    }

    /*展开详情*/
    $('#blog_list').on('click','.description,.name',function(e){
        itemShow(this,1);
        e.preventDefault();e.stopPropagation();
        return false;
    });


    /*关闭详情*/
    $('#blog_list').on('click','.btn_close',function(e){
        window.history.go(-1);
        e.preventDefault();e.stopPropagation();
        return false;
    });




});
