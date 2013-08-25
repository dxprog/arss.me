(function($, Handlebars, undefined) {

    var
        
        months = [ 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'Spetember', 'October', 'November', 'December' ],
        
        $items = null,
        $window = $(window),
        $current = null,
        
        currentItem = 0,
        readingLine = null,
        lastScroll = 0,
        maxItem = 0,
        keepAhead = 5,
        keepBehind = 5,
        
        getFeedTitle = function() {
            
            var
                retVal = 'Unknown',
                i = 0,
                count = 0;
            
            if (typeof window.feeds === 'object') {
                for (i = 0, count = window.feeds.length; i < count; i++) {
                    if (window.feeds[i].id === this.feedId) {
                        retVal = window.feeds[i].name;
                        break;
                    }
                }
            }
            
            return retVal;
            
        },
        
        getDateFormatted = function() {
            var date = new Date(this.date * 1000);
            return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
        },

        prepContents = function() {
            for (var i = keepAhead, count = items.length; i < count; i++) {
                items[i].content = '<!--' + items[i].content + '-->';
                items[i].hidden = true;
                console.log(items[i]);
            }
        },

        updateContents = function() {

            var $item = null,
                $article = null,
                html = null;
            // debugger;
            for (var i = 0, count = $items.length; i < count; i++) {
                $item = $($items[i]);
                $article = $item.find('article');
                if (i >= currentItem - keepBehind && i <= currentItem + keepAhead) {
                    if ($item.hasClass('hidden')) {
                        $item.removeClass('hidden');
                        html = $.trim($article.html());
                        $article.html(html.substr(4, html.length - 7));
                    }
                } else if (!$item.hasClass('hidden')) {
                    $item
                        .height($item.height() + 'px')
                        .addClass('hidden');
                    $article.html('<!--' + $article.html() + '-->');
                }
            }

        },
        
        pageScroll = function(e) {
            
            var
                scrollTop = $window.scrollTop(),
                scrollDown = scrollTop > lastScroll,
                i = currentItem,
                count = null,
                initialItem = currentItem,
                changed = false;
            
            if (scrollDown) {
                for (var i = currentItem + 1, count = $items.length; i < count; i++) {
                    if ($($items[i]).offset().top - scrollTop < readingLine) {
                        $current.removeClass('reading');
                        $current = $($items[i]).addClass('reading');
                        currentItem = i;
                        changed = true;
                        break;
                    }
                }
            } else {
                for (var i = currentItem, count = 0; i >= count; i--) {
                    if ($($items[i]).offset().top - scrollTop < readingLine) {
                        $current.removeClass('reading');
                        $current = $($items[i]).addClass('reading');
                        currentItem = i;
                        changed = true;
                        break;
                    }
                }
            }
            
            if (changed) {
                updateContents();
            }

            if (currentItem > maxItem) {
                $.ajax({
                    url:'/api/index.php',
                    dataType:'json',
                    data:{
                        type:'json',
                        method:'user.updateLastRead',
                        lastRead:$current.attr('data-date')
                    }
                });
                maxItem = currentItem;
            }
            
            lastScroll = scrollTop;
            
        },

        moveAdjacent = function(forward) {
            var $el = forward ? $current.next() : $current.prev();
            if ($el.length) {
                $window.scrollTop($el.offset().top - readingLine + 10);
            }
        }

        keyPress = function(e) {
            var key = e.keyCode || e.charCode,
                keyPressed = false;
            switch (key) {
                case 107:
                    moveAdjacent(false);
                    break;
                case 108:
                    moveAdjacent(true);
                    break;
            }
            
            return !keyPressed;

        },
        
        init = (function() {
        
            Handlebars.registerHelper('feedTitle', getFeedTitle);
            Handlebars.registerHelper('dateFormatted', getDateFormatted);
            prepContents();
            document.getElementById('container').innerHTML = Handlebars.templates.itemList(items);
            $items = $('.item');
            $current = $($items[0]).addClass('reading');
            $window.on('scroll', pageScroll);
            pageScroll();
            readingLine = $window.height() / 3;
            $(document).on('keypress', keyPress);
            
        }());

}(jQuery, Handlebars));