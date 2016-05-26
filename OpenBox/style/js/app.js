/*
Powered by OpenUE.com
version: V0.1 beta
 */
$(document).ready(function() {

		var show = true;
		$('#menubox').click(function() {
			if( show == true ) {
				$('#menubox').addClass('menuout');
				$('.menubox').css({
					bottom: '0',
					transition: '0.5s'
				});
				show = false;
			} else {
				$('#menubox').removeAttr('class');
				$('.menubox').removeAttr('style');
				show = true;
			}
		});

	$('#comment').click(function() {
		$('.send .group').css('bottom','0');
	});
	$('#sendqq').click(function() {
		var qqnum = $('#qq').val();
		var ename = 'QQ网友';
		var email = '@qq.com';
		var myurl = '.qzone.qq.com';
		var numty = /^[1-9][0-9]{4,10}$/;
		if( !numty.test(qqnum) ) {
			$('#qq').css('background','red');
			alert('您输入的QQ号有误！');
			return false;
		}
		$('#author').val(ename);
		$('#email').val(qqnum + email);
		$('#url').val('http://' + qqnum + myurl);
	});

	$(function() {
		var $a = $('#archives'),
			$m = $('.al_mon', $a),
			$l = $('.al_post_list', $a),
			$l_f = $('.al_post_list:first', $a);
		$l.hide();
		$l_f.show();
		$m.css('cursor', 's-resize').on('click', function(){
			$(this).next().slideToggle(400);
		});
		var animate = function(index, status, s) {
			if (index > $l.length) {
				return;
			}
			if (status == 'up') {
				$l.eq(index).slideUp(s, function() {
					animate(index+1, status, (s-10<1)?0:s-10);
				});
			} else {
				$l.eq(index).slideDown(s, function() {
					animate(index+1, status, (s-10<1)?0:s-10);
				});
			}
		};
		$('#al_expand_collapse').on('click', function(e){
			e.preventDefault();
			if ( $(this).data('s') ) {
				$(this).data('s', '');
				animate(0, 'up', 100);
			} else {
				$(this).data('s', 1);
				animate(0, 'down', 100);
			}
		});
	});

});
/*comment end*/
SidebarFollow = function() {
	this.config = {
		element: null, // 处理的节点
		distanceToTop: 0 // 节点上边到页面顶部的距离
	};
	this.cache = {
		originalToTop: 0, // 原本到页面顶部的距离
		prevElement: null, // 上一个节点
		parentToTop: 0, // 父节点的上边到顶部距离
		placeholder: jQuery("<div class='aside' style='background:none;border:none;'>") // 占位节点
	};
};
SidebarFollow.prototype = {
init: function(config) {
	this.config = config || this.config;
	var _self = this;
	var element = jQuery(_self.config.element);
	// 如果没有找到节点, 不进行处理
	if(element.length <= 0) {
		return;
	}
	// 获取上一个节点
	var prevElement = element.prev();
	while(prevElement.is(':hidden')) {
		prevElement = prevElement.prev();
		if(prevElement.length <= 0) {
			break;
		}
	}
	_self.cache.prevElement = prevElement;
	// 计算父节点的上边到顶部距离
	var parent = element.parent();
	var parentToTop = parent.offset().top;
	var parentBorderTop = parent.css('border-top');
	var parentPaddingTop = parent.css('padding-top');
	_self.cache.parentToTop = parentToTop + parentBorderTop + parentPaddingTop;
	// 滚动屏幕
	jQuery(window).scroll(function() {
		_self._scrollScreen({element:element, _self:_self});
	});
	// 改变屏幕尺寸
	jQuery(window).resize(function() {
		_self._scrollScreen({element:element, _self:_self});
	});
},
/**
 * 修改节点位置
 */
_scrollScreen: function(args) {
	var _self = args._self;
	var element = args.element;
	var prevElement = _self.cache.prevElement;
	// 获得到顶部的距离
	var toTop = _self.config.distanceToTop;
	// 如果 body 有 top 属性, 消除这些位移
	var bodyToTop = parseInt(jQuery('body').css('top'), 10);
	if(!isNaN(bodyToTop)) {
		toTop += bodyToTop;
	}
	// 获得到顶部的绝对距离
	var elementToTop = element.offset().top - toTop;
	// 如果存在上一个节点, 获得到上一个节点的距离; 否则计算到父节点顶部的距离
	var referenceToTop = 0;
	if(prevElement && prevElement.length === 1) {
		referenceToTop = prevElement.offset().top + prevElement.outerHeight();
	} else {
		referenceToTop = _self.cache.parentToTop - toTop;
	}
	// 当节点进入跟随区域, 跟随滚动
	if(jQuery(document).scrollTop() > elementToTop) {
		// 添加占位节点
		var elementHeight = element.outerHeight();
		_self.cache.placeholder.css('height', elementHeight).insertBefore(element);
		// 记录原位置
		_self.cache.originalToTop = elementToTop;
		// 修改样式
		element.css({
			top: toTop + 'px',
			position: 'fixed'
		});
	// 否则回到原位
	} else if(_self.cache.originalToTop > elementToTop || referenceToTop > elementToTop) {
		// 删除占位节点
		_self.cache.placeholder.remove();
		// 修改样式
		element.removeAttr("style");
	}
}
};

/* active aside float */
(new SidebarFollow()).init({
    element : jQuery('#menubar'),
    distanceToTop : 15
});

/* menubar float end */

if ($('#go2top').length) {
    var scrollTrigger = 100, // px
        backToTop = function () {
            var scrollTop = $(window).scrollTop();
            if (scrollTop > scrollTrigger) {
                $('#go2top').addClass('show');
            } else {
                $('#go2top').removeClass('show');
            }
        };
    backToTop();
    $(window).on('scroll', function () {
        backToTop();
    });
    $('#go2top').on('click', function (e) {
        e.preventDefault();
        $('html,body').animate({
            scrollTop: 0
        }, 700);
    });
}

/* go top end */