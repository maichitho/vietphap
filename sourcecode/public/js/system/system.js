$(document).ready(function() {
    var height = $(window).height();
    $(".content").css("min-height", height - 111);

   
});
// Utils

var FomartString = {
    getOriginFomart: function(str) {
        return str.replace(/\r?\n/g, "<br />");
    },
    getTextFormart: function(str) {
        return str.replace(/(\<br\s\/\>)/g, " ");
    }
}

function strimStringOfDom(str) {
    return str.length > 45 ? str.substring(0, 45) + "..." : str;
}
/**
 * Group object by language and return array of object by lang
 * @param {array} objs
 * @returns {array} reVals
 */
function mapObjectToLang(objs) {
    var reVals = new Array();
    for (var i = 0; i < objs.length; i++) {
        var lang = objs[i]["languageCode"];
        if (reVals[lang] !== undefined) {
            reVals[lang].push(objs[i]);
        } else {
            reVals[lang] = new Array();
            reVals[lang].push(objs[i]);
        }
    }
    return reVals;
}


jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});



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

function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
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