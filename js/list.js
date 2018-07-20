$(function(){
    // $('#blog_list').on('wheel mousewheel','.item_bg',function(){
    //     anime.remove(this);
    //     anime({
    //         targets: this,
    //         scale: [
    //             { value: 0.95, duration: 250 },
    //             { value: 1, duration: 250 }
    //         ],
    //     });
    // });
    var isPjaxEnable = window.history && window.history.pushState && window.history.replaceState &&
            // pushState isn't reliable on iOS until 5.
            !navigator.userAgent.match(/((iPod|iPhone|iPad).+\bOS\s+[1-4]\D|WebApps\/.+CFNetwork)/);

    // 列表页数据
    var linkThisPage = window.location.href;
    var titleThisPage = $('title').text();

    /*禁止标签超链接*/
    $('#blog_list').on('click','.name',function(e){
        if (isPjaxEnable)
        {
            e.preventDefault();e.stopPropagation();
            return false;
        }
    });


    /*展开详情*/
    $('#blog_list').on('click','.item_bg,.name',function(e){
        if (!isPjaxEnable)
        {
            return true;
        }

        var $this = $(this).closest('.item_bg');
        if ($this.hasClass('item_fixed'))
        {
            return ;
        }


        var link = $this.find('.name').attr('href');
        window.history.replaceState(null, $this.find('.name').text(), link);
        if (!$this.find('.content').hasClass('content_load'))
        {
            $.get(link).then(function(response){
                $this.find('.content').html(response).addClass('content_load');
            });
        }

        bodyNice.locked = true;

        var position = $this.offset();
        // position.top = position.top - (document.body.scrollTop + document.documentElement.scrollTop);
        position.top = position.top - bodyNice.scrollTop();
        $this.data('position',position);

        $this.addClass('item_fixed').css({'top':position.top+'px'}).addClass('anime').addClass('item_showing');
        $this.find('.description').hide();
        $this.find('.content').fadeIn();


        var _this = $this[0];
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
                var scrollTop = document.scrollingElement.scrollTop;
                $('body').addClass('body-prevent-class').data('scrollTop',scrollTop).css('top',-scrollTop + 'px');
                $this.removeClass('anime').removeClass('item_showing');
                $this.niceScroll({
                    horizrailenabled:false,
                });
                if (_hmt){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
        e.preventDefault();e.stopPropagation();
        return false;
    });


    /*关闭详情*/
    $('#blog_list').on('click','.btn_close',function(e){
        var $this = $(this).closest('.item_bg');
        if (!$this.hasClass('item_fixed'))
        {
            return ;
        }

        window.history.replaceState(null, titleThisPage, linkThisPage);


        $this.addClass('anime').addClass('item_hiding');
        $('body').removeClass('body-prevent-class').css('top','0px');
        document.scrollingElement.scrollTop =  $('body').data('scrollTop');
        $this.find('.content').hide();
        $this.find('.description').fadeIn();
        $this.css({
                    });
        var position = $this.data('position');


        var _this = $this[0];
        anime.remove(_this);
        anime({
            targets: _this,
            scale: 1,
            top:(position.top)+'px',
            left:(position.left)+'px',
            width:$this.closest('.item_li').width()+'px',
            height: '256px',
            duration:500,
            complete: function(anim) {
                $this.removeClass('item_fixed').removeClass('item_hiding');
                $this.css({
                            'top':'',
                            'left':'',
                            'width':'',
                            'height':'',
                        });
                $this.removeClass('anime');
                $this.getNiceScroll().remove();
                bodyNice.locked = false;
                if (_hmt){_hmt.push(['_trackPageview', window.location.href]);}
            }
        });
        e.preventDefault();e.stopPropagation();
        return false;
    });


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
                if (_hmt){_hmt.push(['_trackPageview', link]);}
                $this.remove();
                $('#blog_list').append(response);
                bodyNice.resize();
            });
        });
        e.preventDefault();e.stopPropagation();
        return false;
    });


});
