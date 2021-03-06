var attribute = (function ($, undefined) {
    'use strict';
    /**
     * bootstrapSelect pour l'équipe extérieur
     */
    function valueSelect(){
        if($('#attributes_id').val() !== ''){
            var id = $('#attributes_id').val();
            var cus = $('#filter-attributes').find('li[data-value="'+id+'"]');
            if(!cus.length) {
                $('#attributes').bootstrapSelect('clear');
            } else {
                var cu = $(cus[0]);
                $('#attributes').bootstrapSelect('select',cu);
            }
        }
        $('#attributes_id').on('focusout',function(){
            var id = $(this).val();
            if(id !== '') {
                var cus = $('#filter-attributes').find('li[data-value="'+id+'"]');
                if(!cus.length) {
                    $('#attributes').bootstrapSelect('clear');
                    $('#attributes_id').val('');
                } else {
                    var cu = $(cus[0]);
                    $('#attributes').bootstrapSelect('select',cu);
                }
            }
        });
    }
    return {
        run: function (controller){
            valueSelect();
            $( ".ui-sortable" ).sortable({
                items: "> tr",
                cursor: "move",
                axis: "y",
                update: function(){
                    var serial = $( ".ui-sortable" ).sortable('serialize');
                    $.jmRequest({
                        handler: "ajax",
                        url: controller+'&action=order',
                        method: 'POST',
                        data : serial,
                        success:function(e){
                            $.jmRequest.initbox(e,{
                                    display: false
                                }
                            );
                        }
                    });
                }
            });
            $( ".ui-sortable" ).disableSelection();
        }
    };
})(jQuery);