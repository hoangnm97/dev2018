// $.fn.editable.defaults.mode = 'inline';

$(function(){

    var panda_table = $('.panda-table');
    var panda_table_render = $('.pandaTableRender');
    if(panda_table.length > 0 && panda_table_render.length > 0){
        var tw = panda_table.width();
        panda_table_render.css('min-width', tw +'px');
    }


});
