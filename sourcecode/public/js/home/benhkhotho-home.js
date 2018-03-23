$(document).ready(function() {
    $(".top-menu .menu-item").mouseover(function() {
        $(this).addClass("menu-item--active");
        $(this).find(".popup-menu").show();
        var top = $(this).offset().top;
        var left = $(this).offset().left;
        $(this).find(".popup-menu").offset({
            top: top + 40,
            left: left
        });
    }).mouseout(function() {
        $(this).removeClass("menu-item--active");
        $(this).find(".popup-menu").hide();
    });

    $(".popup-menu").mouseout(function() {
        $(this).hide();
    });

    $('.scroll-up').on('click', function(e) {
        e.preventDefault();
        var target = this.hash,
                $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': 0
        }, 900, 'swing', function() {
            window.location.hash = target;
        });
    });
    
   
    $(window).on("scroll", function() {
        if ($(this).scrollTop() > 100) {
            $(".b-header-panel").addClass("b-header-fix");
            if ($(".f-menu-hr > ul:first > li:first").hasClass("menu-item-selected")) {
                $(".f-bar-layer").hide();
                $(".f-toolbar-layer").hide();
            }
            $(".b-header-panel-moblie").addClass("b-header-fix");
            $(".b-header-panel-moblie .f-mobile-text").hide();
        } else {
            $(".b-header-panel").removeClass("b-header-fix");
            $(".f-bar-layer").show();
            $(".f-toolbar-layer").show();
            $(".b-header-panel-moblie").removeClass("b-header-fix");
            $(".b-header-panel-moblie .f-mobile-text").show();
        }
        
         if ($(this).scrollTop() > 100) {
           
            $(".f-menu-extra-item").fadeIn();
        } else {
           
            $(".f-menu-extra-item").fadeOut();
        }
        
        

        if ($(this).scrollTop() > 50) {
            $(".b-header-panel-moblie").addClass("b-header-fix");
            $(".b-header-panel-moblie .f-mobile-text").hide();
        } else {
            $(".b-header-panel-moblie").removeClass("b-header-fix");
            $(".b-header-panel-moblie .f-mobile-text").show();
        }
    });

});

//Utils
/**
 * Devide 1 array into x array
 * @param {type} oldArr
 * @returns {undefined}
 */
function prepareArrayByColumn(oldArr, num) {
    var retVal = new Array();
    var j = 0;
    for (var i = 0; i < oldArr.length; i++) {
        if (j < num - 1) {
            if (retVal[j] == undefined) {
                retVal[j] = new Array();
            }
            retVal[j].push(oldArr[i]);
            j++;
        } else {
            if (retVal[j] == undefined) {
                retVal[j] = new Array();
            }
            retVal[j].push(oldArr[i]);
            j = 0;
        }
    }
    return retVal;
}

/**
 * Randomize array element order in-place.
 * Using Fisher-Yates shuffle algorithm.
 */
function shuffleArray(array) {
    for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
    }
    return array;
}

// Utils
// Link: http://thecodecentral.com/2008/02/21/a-useful-javascript-image-loader
function addListener(element, type, expression, bubbling)
{
    bubbling = bubbling || false;
    if (window.addEventListener) { // Standard
        element.addEventListener(type, expression, bubbling);
        return true;
    } else if (window.attachEvent) { // IE
        element.attachEvent('on' + type, expression);
        return true;
    } else
        return false;
}

function cutName(name, standardNumCharacter) {
    if (name.length > standardNumCharacter) {
        var temp = name.substr(0, standardNumCharacter - 2);
        if (temp.charAt(temp.length - 1) != " ")
            name = temp.substring(0, temp.lastIndexOf(" ")).trim();
        else
            name = temp.trim();
        name += ".."
    }
    return name;
}

var ImageLoader = function(url) {
    this.url = url;
    this.image = null;
    this.loadEvent = null;
};

ImageLoader.prototype = {
    load: function() {
        this.image = document.createElement('img');
        var url = this.url;
        var image = this.image;
        var loadEvent = this.loadEvent;
        addListener(this.image, 'load', function(e) {
            if (loadEvent != null) {
                loadEvent(url, image);
            }
        }, false);
        this.image.src = this.url;
    },
    getImage: function() {
        return this.image;
    }
};

//Utils
function convertMoney(orginalPrice) {
    var returnPrice = parseFloat(orginalPrice).toLocaleString("vi-VN", {
        style: "currency",
        currency: "VND"
    });
    return returnPrice;
}

function formatDate(dateObject) {
    var d = new Date(dateObject);
    var day = d.getDate();
    var month = d.getMonth() + 1;
    var year = d.getFullYear();
    if (day < 10) {
        day = "0" + day;
    }
    if (month < 10) {
        month = "0" + month;
    }
    var date = day + "/" + month + "/" + year;

    return date;
}


function resizeImages(jObj, parentW, parentH) {
    var loader = new ImageLoader(jObj.attr("src"));
    loader.loadEvent = function(url, image) {
        var width = jObj.width(),
                height = jObj.height();
        if (width > parentW) {
            if (height > parentH) {
                if (width / parentW > height / parentH) {
                    jObj.width(parentW);
                    jObj.css("height", "auto");
                    jObj.css("margin-top", (parentH - jObj.height()) / 2 + "px");
                } else {
                    jObj.height(parentH);
                    jObj.css("width", "auto");
                }
            } else {
                jObj.width(parentW);
                jObj.css("height", "auto");
                jObj.css("margin-top", (parentH - jObj.height()) / 2 + "px");
            }
        } else {
            if (height > parentH) {
                jObj.height(parentH);
                jObj.css("width", "auto");
            } else {
                if (width / parentW > height / parentH) {
                    jObj.width(parentW);
                    jObj.css("height", "auto");
                    jObj.css("margin-top", (parentH - jObj.height()) / 2 + "px");
                } else {
                    jObj.height(parentH);
                    jObj.css("width", "auto");
                }
            }
        }
    };
    loader.load();
}

//function strimStringOfDom(str) {
//    console.log(str);
//    return str.length > 40 ? str.substring(0, 40) + "..." : str;
//}

function getHourMinute(dateObject) {
    var d = new Date(dateObject);
    var hour = d.getHours();
    var minute = d.getMinutes();
    return hour + ":" + minute;
}
function getDay(dateObject) {
    var d = new Date(dateObject);
    var day = d.getDate();
    return day;
}
function getMonth(dateObject) {
    var d = new Date(dateObject);
    var month = d.getMonth() + 1;
    return month;
}
function getYear(dateObject) {
    var d = new Date(dateObject);
    var year = d.getFullYear();
    return year;
}

function strimStringOfDom(str, count) {
    if (count == undefined) {
        count = 30;
    }
    if(str==undefined)
        return "";
    return str.length > count ? str.substring(0, count) + "..." : str;
}

function getWordOfString(str, count) {
    if (str === undefined) {
        return "";
    }
    var retVal = "";
    var arrStr = str.split(" ");
    for (var i = 0; i < arrStr.length; i++) {
        count--;
        if (count < 0) {
            break;
        }
        retVal += " " + arrStr[i];
        if (count == 0) {
            retVal += "...";
        }
    }
    return retVal;
}

function change_alias(alias)
{
    var str = "";
    if (typeof alias === "object" || alias === undefined) {
        return "";
    }
    str = alias;
    str = str.toLowerCase();
    str = str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g, "a");
    str = str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g, "e");
    str = str.replace(/ì|í|ị|ỉ|ĩ/g, "i");
    str = str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g, "o");
    str = str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g, "u");
    str = str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g, "y");
    str = str.replace(/đ/g, "d");
    str = str.replace(/!|@|%|\^|\*|\(|\)|\+|\=|\<|\>|\?|\/|,|\.|\:|\;|\'| |\"|\&|\#|\[|\]|~|$|_/g, "-");
    /* tìm và thay thế các kí tự đặc biệt trong chuỗi sang kí tự - */
    str = str.replace(/-+-/g, "-"); //thay thế 2- thành 1-
    str = str.replace(/^\-+|\-+$/g, "");
    //cắt bỏ ký tự - ở đầu và cuối chuỗi 
    return str;
}
