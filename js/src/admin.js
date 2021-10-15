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
        run: function (){
            valueSelect();
        }
    };
})(jQuery);