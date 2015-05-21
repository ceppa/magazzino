jQuery.warehouse = function(options) {
    var opts = jQuery.extend(
                    {},
                    jQuery.warehouse.defaults,
                    options
                );
};

jQuery.warehouse.defaults = {
    serverAddress:  "localhost",
    name:           "ASTA Warehouse",
    dbName:         "warehouse"
};

jQuery.warehouse.item = {
    serverAddress:  "localhost",
    name:           "ASTA Warehouse",
    dbName:         "warehouse"
};