Globals = {
    websocketHost: null,
    websocketPort: null
};

Lib = {
    relativeTime: function(current, previous) {
        var msPerMinute = 60 * 1000;
        var msPerHour = msPerMinute * 60;
        var msPerDay = msPerHour * 24;
        var msPerMonth = msPerDay * 30;
        var msPerYear = msPerDay * 365;

        var elapsed = current.getTime() - previous.getTime();

        if (elapsed < msPerMinute) {
             return Math.round(elapsed/1000) + ' segundos';   
        } else if (elapsed < msPerHour) {
             return Math.round(elapsed/msPerMinute) + ' minutos';   
        } else if (elapsed < msPerDay ) {
             return Math.round(elapsed/msPerHour ) + ' horas';   
        } else if (elapsed < msPerMonth) {
            return Math.round(elapsed/msPerDay) + ' días';   
        } else if (elapsed < msPerYear) {
            return Math.round(elapsed/msPerMonth) + ' meses';   
        } else {
            return Math.round(elapsed/msPerYear ) + ' años';   
        }
    }
};

Admin = {
    lastReadingByChannel: {},
    
    init: function () {
        setInterval(Admin.updateChannelValueTimes, 900);
        $(document).on("caecepic.websocket.message", function(event, data) {
            Admin.updateChannelValues(data);
        });
    },
    
    updateChannelValues: function(data) {
        for (var i in data.readings) {
            var reading = data.readings[i];
            Admin.lastReadingByChannel[reading.channel] = reading;
        }
        
        for (var i in Admin.lastReadingByChannel) {
            var reading = Admin.lastReadingByChannel[i];
            var unitName = $("#admin_channel_unit_" + reading.channel).html();
            $("#admin_channel_value_" + reading.channel).html(reading.convertedData + " " + unitName + " (raw: " + reading.rawData + ")");
        }
        
        Admin.updateChannelValueTimes();
    },
    
    updateChannelValueTimes: function() {
        for (var i in Admin.lastReadingByChannel) {
            var reading = Admin.lastReadingByChannel[i];
            $("#admin_channel_value_time_" + reading.channel).html('leído hace ' + Lib.relativeTime(new Date(), new Date(reading.readedAt)));
        }
    }
};

Charts = {
    init: function() {
        $(document).on("caecepic.websocket.message", function(event, data) {
            Charts.updateChannelValues(data);
        });
        
        //TODO: Inicializar el Highstock
    },
    
    updateChannelValues: function(data) {
        //TODO: Actualizar los gráficos
    }
};

App = {
    init: function() {
        App.initWebsocket();
    },
    
    initWebsocket: function() {
        var websocket = new WebSocket("ws://" + Globals.websocketHost + ":" + Globals.websocketPort);
        
        websocket.onopen = function(event) {
            console.info("Se abrió la conexión WebSocket");
        };
        
        websocket.onclose = function(event) {
            console.info("Se cerró la conexión WebSocket");
        };
        
        websocket.onmessage = function(event) {
            console.log(event.data);
            var data = JSON.parse(event.data);
            $(document).trigger("caecepic.websocket.message", data);
        };
        
        websocket.onerror = function(event) {
            console.error("Se cerró la conexión WebSocket");
            alert("Están ocurriendo problemas para comunicarse con el servidor, por favor, recargué la página.");
        };
    }
};
