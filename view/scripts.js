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
        
        pageScroll = function(e) {
            
            var
                scrollTop = $window.scrollTop(),
                scrollDown = scrollTop > lastScroll,
                i = currentItem,
                count = null,
                initialItem = currentItem;
            
            console.log(scrollTop, lastScroll);
            
            if (scrollDown) {
                for (var i = currentItem + 1, count = $items.length; i < count; i++) {
                    if ($($items[i]).offset().top - scrollTop < readingLine) {
                        $current.removeClass('reading');
                        $current = $($items[i]).addClass('reading');
                        currentItem = i;
                        break;
                    }
                }
            } else {
                for (var i = currentItem, count = 0; i >= count; i--) {
                    if ($($items[i]).offset().top - scrollTop < readingLine) {
                        $current.removeClass('reading');
                        $current = $($items[i]).addClass('reading');
                        currentItem = i;
                        break;
                    }
                }
            }
            
            if (currentItem > maxItem) {
                $.ajax({
                    url:'/feeed/api/index.php',
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
        
        init = (function() {
        
            Handlebars.registerHelper('feedTitle', getFeedTitle);
            Handlebars.registerHelper('dateFormatted', getDateFormatted);
            document.getElementById('container').innerHTML = Handlebars.templates.itemList(items);
            $items = $('.item');
            $current = $($items[0]).addClass('reading');
            $window.on('scroll', pageScroll);
            pageScroll();
            readingLine = $window.height() / 3;
            
        }());

}(jQuery, Handlebars));