document.writeln("<script type='text/javascript' src='pluginDetect.js'></script>");
document.writeln("<script type='text/javascript' src='ua-parser.js'></script>");
document.writeln("<script type='text/javascript' src='message.js' charset='cp1251'></script>");

var warnBrowser;
var warnOS;
var warnJava;
var notStable;
var load;
var bit;
var os;
var nosup;
var gotoInfo;
var parser;
var result;

var isWindows = navigator.platform.indexOf("Win") == 0;
var isInitialized = false;
var language = navigator.languages ? navigator.languages[0] : (navigator.language || navigator.userLanguage);

var JavaChecker = {
    isInitialized : false, version : "1.0", name : "JavaChecker", getJavaVersion : function () {
        if (!isInitialized)
            this.init(null);
        //получить все установленные плагины Java
        try {
            var obj = PluginDetect.getInfo('Java');
            var allVersions = obj.All_versions;// array
            var versions = allVersions[0];
            for (var k = 1;allVersions.length > k;k++) {
                versions = versions + ";" + allVersions[k];
            }
            return versions.replace(/,/g, ".");
        }
        catch (e) {
            ;
        }
        return null;
    },
    init : function (lang) {
        parser = new UAParser();
        result = parser.getResult();

        var lang_ = lang;
         if (lang) {
            lang = lang.toLowerCase();
            if (lang.indexOf("ru") != -1 || lang.indexOf("uk") != -1 || lang.indexOf("en") != -1) {
                lang_ = lang;
            }
        } else {
            lang_ = language;
        }
	try {
	    dhtmlx.setLanguage(lang_);
	} catch (ex) {
		;
	}
        if (lang_.indexOf("ru") !=  - 1) {
            language = "ru";
            warnBrowser = " не поддерживается для работы с Java.\nИспользуйте другой браузер (Интернет-навигатор).\n";
            warnJava = "Java не установлена или не разрешена в браузере.\n";
            bit = "Необходимо загрузить и/или разрешить Java.\n";
            load = "Загрузить Java?\n";
            os = "Ваша операционная система ";
            nosup = " не поддерживается.\nНеобходимо обновить до более новой версии.";
            gotoInfo = "\n\nПерейти на страницу детальной информации?";
            notStable = "Так как работает нестабильно при операциях Java более 10-12 сек.\n";
        }
        else if (lang_.indexOf("uk") !=  - 1) {
            language = "uk";
            warnBrowser = " не підтримується для роботи з Java.\nВикористовуйте інший браузер (Інтернет-навігатор).\n";
            warnJava = "Java не встановлена або не дозволена в браузері.\n";
            bit = "Необхідно завантажити та/або дозволити Java.\n";
            load = "Завантажити Java?\n";
            os = "Ваша операційна система ";
            nosup = " не підтримується.\nНеобхідно оновити до більш нової версії.";
            gotoInfo = "\n\nПерейти на сторінку детальної інформації?";
            notStable = "Так як працює нестабільно при операціях Java більше 10-12 сек.\n";
        }
        else {
            language = "en";
            warnBrowser = " is not supported with Java.\nUse a different browser.\n";
            warnJava = "Java is not installed or is not allowed in browser.\n";
            bit = bit = "You need to download and/or allow Java.\n";
            load = "Download Java?\n";
            os = "Your operating system ";
            nosup = " is not supported.\nPlease update to a newer version.";
            gotoInfo = "\n\nGo to the details page?";
            notStable = "As the computer becomes unstable when Java operations more than 10-12 seconds.\n";
        }
        isInitialized = true;
    },
    isIE : function () {
        return PluginDetect.browser.isIE
    },
    getMajorVersion : function (version_) {
        var ind = version_.indexOf(",");
        if (ind ==  - 1)
            ind = version_.indexOf(".");

        if (ind !=  - 1)
            ind = parseInt(version_.substring(0, ind));
        else 
            ind = parseInt(version_);

        return ind;
    },
    getBrowserInfo : function () {
        if (!isInitialized)
            this.init(null);
        return (result.browser.name + " " + result.browser.version);
    },
    getBrowserWarning : function () {
        if (!isInitialized)
            this.init(null);
        var os = this.getOS();
        var nm = result.browser.name;
        var mver = this.getMajorVersion(result.browser.version);
        var warning = "";
        if (nm.indexOf("Chrome") !=  - 1) {
            if (mver > 42) {
                warning = this.getBrowserInfo() + warnBrowser;
            }
        }
        else if (nm.indexOf("Firefox") !=  - 1) {
            if (mver > 41 && isWindows) {
                //warning = this.getBrowserInfo() + warnBrowser + notStable;
            }
        }  
        else if (nm.indexOf("Safari") !=  - 1 && isWindows) {            
            warning = this.getBrowserInfo() + warnBrowser;
        }
        else if (nm.indexOf("Safari") !=  - 1) {
            if (6 > mver) {
                warning = this.getBrowserInfo() + warnBrowser;
            }
        }      
        else if (nm.indexOf("Edge") !=  - 1) {
            warning = this.getBrowserInfo() + warnBrowser;
        }
        else if (nm.indexOf("Opera") !=  - 1) {
            if (mver > 33) {
                if (os.indexOf("Windows 7") != -1)
                    warning = this.getBrowserInfo() + warnBrowser;
            }
        }
        else if (nm.indexOf("Yandex") !=  - 1) {
            if (mver > 15) {                
                warning = this.getBrowserInfo() + warnBrowser;
            }
        }
        return warning;
    },
    verifyJava : function () {
        if (!isInitialized)
            this.init(null);

        var win;
        var browserWarning = this.getBrowserWarning();
        var le = language;
        if (le.indexOf("uk") !=  - 1)
            le = "ua";

        if (browserWarning) {
            //нельзя работать с Java            
            userInput = confirm(browserWarning + gotoInfo);
            if (userInput) {
                win = window.open("http://www.itsway.kiev.ua/index.php?language=" + le + "&main_managemen=prodaction&managemen=faq_info#q_2", "_self");
                win.focus();
            }
            return false;
        }

        var os = this.getOS();
        if (warnOS) {
            //нельзя работать            
            userInput = confirm(warnOS + gotoInfo);
            if (userInput) {
                win = window.open("http://www.itsway.kiev.ua/index.php?language=" + le + "&main_managemen=prodaction&managemen=faq_info#q_1", "_self");
                win.reload();
                win.focus();
            }
            return false;
        }
        else {
            var javaVersion = this.getJavaVersion();
            if (!javaVersion || javaVersion.indexOf("undefined") !=  - 1) {
                if (isWindows) {
                    warnJava = warnJava + bit;
                }

                userInput = confirm(warnJava + load);
                if (userInput) {
                    if (PluginDetect.browser.isOpera)
                        window.open("http://java.com/ru/download/windows_manual.jsp", "_self");
                    else {
                        if (isWindows)
                            window.open("http://java.com/ru/download/windows_xpi.jsp", "_self");
                        else 
                            window.open("http://java.com/ru/download/testjava.jsp", "_self");
                    }
                }
                return false;
            }
            else {
                //alert("Java установлена и разрешена.");
                return true;
            }
        }
    },
    getOS : function () {
        warnOS = "";
        if (!isInitialized)
            this.init(null);

        OSName = result.os.name + " " + result.os.version;
        var nm = result.os.name;
        var ver = result.os.version;

        var ua = window.navigator ? navigator.userAgent || "" : "";
        //"Windows NT 5.1; SV1;"
        var pos;
        if (ua.indexOf("Windows NT 5.1") !=  - 1) {
            OSName = "Windows XP SP3";
            if (ua.indexOf("SV1;") !=  - 1) {
                OSName = "Windows XP SP2";
                //warnOS = os + OSName + nosup;//обновить ОС
                //если SP2 через update обновлена до SP3, то все равно остается "метка" SV1, т.е. что это SP2                
            }
        }
        else if (nm.indexOf("Mac OS") !=  - 1) {
            ver1 = this.getMajorVersion(ver);
            if (ver1 == 10) {
                pos = ver.indexOf(".");
                if (pos !=  - 1) {
                    ver = ver.substring(pos + 1, ver.length);
                    ver1 = this.getMajorVersion(ver);
                    if (7 > ver1) {
                        warnOS = OSName + nosup;
                    }                    
                }
            }
        }

        ua = ua.toLowerCase();
        if (OSName.indexOf("Win") !=  - 1) {
            if (ua.indexOf("wow64") !=  - 1 || ua.indexOf("win64") !=  - 1) {
                OSName = OSName + " (64-bit)";
            }
            else {
                OSName = OSName + " (32-bit)";
            }
        }
        else {
            if (ua.indexOf("x86_64") !=  - 1 || ua.indexOf("amd64") !=  - 1 || ua.indexOf("intel64") !=  - 1 || ua.indexOf("em64t") !=  - 1 || ua.indexOf("ia64") !=  - 1) {
                OSName = OSName + " (64-bit)";
            }
            else if (ua.indexOf("x86") !=  - 1 || ua.indexOf("i286") !=  - 1 || ua.indexOf("i386") !=  - 1 || ua.indexOf("i486") !=  - 1 || ua.indexOf("i586") !=  - 1 || ua.indexOf("i686") !=  - 1) {
                OSName = OSName + " (32-bit)";
            }
        }
        return OSName;
    }

};