<script>
    /** Add Hunt **/
    let huntURL = "{{ route('admin.sponser-hunts.hunt-html') }}"
    $(document).on('click', '.add-hunt', function() {
        if(validate()){
            let lastIndex = parseInt($('.hunt-container').children().last().attr('index'));
            $.get(huntURL, {index: lastIndex}, function(response) {
                $('.hunt-container').append(response);
                let addBtnOfLast = $('.hunt-container').children().last().find('.add-hunt');
                $('<button type="button" class="btn btn-danger remove-hunt">-</button>').insertBefore(addBtnOfLast);
            });
            $(this).removeClass('btn-success add-hunt').addClass('btn-danger remove-hunt').text('-');
        }
    });
    $(document).on('click', '.remove-hunt', function() {

        $(this).parent().parent().remove();
        let huntContainerChildren = $('.hunt-container').children('.single-hunt-container'); 
        if(huntContainerChildren.length > 1) {
            let addBtnOfLast = huntContainerChildren.last().find('.remove-hunt');
            $('<button type="button" class="btn btn-success add-hunt">+</button>').insertAfter(addBtnOfLast);
        }else {
            let btn = huntContainerChildren.last().find('.remove-hunt');
            $(btn).removeClass('btn-danger remove-hunt').addClass('btn-success add-hunt').text('+');
        }
    });

    /** Add Clue **/
    let clueURL = "{{ route('admin.sponser-hunts.clue-html') }}"
    $(document).on('click', '.add-clue', function() {
        if(validate()){
            let huntElem = $(this).parents().closest('.single-hunt-container');
            let parentIndex = parseInt(huntElem.attr('index')-1);

            /** Clue Section **/
            let children = $('.clue-container').children();
            let indexAttrOfLast = parseInt($('.clue-container').children().last().attr('index'));
            $.get(clueURL, {index: indexAttrOfLast, parentIndex: parentIndex}, function(response) {
                $(huntElem).children('.clue-container').append(response);
                let addBtnOfLast = $(huntElem).children('.clue-container').last().find('.add-clue');
                $('<button type="button" class="btn btn-danger remove-clue">-</button>').insertBefore(addBtnOfLast);
            });
            $(this).removeClass('btn-success add-clue').addClass('btn-danger remove-clue').text('-');
        }
    });

    $(document).on('click', '.remove-clue', function() {
        let huntElem = $(this).parents().closest('.single-hunt-container');
        $(this).parent().remove();

        let clueContainerChildren = $(huntElem).children('.clue-container').last().children('.single-clue-container'); 
        if(clueContainerChildren.length > 1) {
            let addBtnOfLast = clueContainerChildren.last().find('.remove-clue');
            $('<button type="button" class="btn btn-success add-clue">+</button>').insertAfter(addBtnOfLast);
        }else {
            let btn = clueContainerChildren.last().find('.remove-clue');
            $(btn).removeClass('btn-danger remove-clue').addClass('btn-success add-clue').text('+');
        }
    });

    let fails;

    function hasAttr(element, attribute) {
        var attr = $(element).attr(attribute);
        if (typeof attr !== typeof undefined && attr !== false) {
            return {status: true, value: attr};
        }else{
            return {status: false, value: attr};
        }
    }

    function focusTheError(element, message) {
        $(element).focus();
        $(element).parent().removeClass('has-success').addClass('has-error');
        fails++;
        toastr.error(message);
        return true;
    }

    function validateFromAttribute(element) {
        let value = $(element).val();
        let minimumRule = hasAttr(element, 'minlength');
        let requiredRule = hasAttr(element, 'required');

        if(requiredRule.status && !value) {
            focusTheError(element, $(element).attr('alias-name') + " is required field.");
            return false;
        }

        if(minimumRule.status && parseInt(minimumRule.value) > parseInt(value.length)) {
            focusTheError(element, $(element).attr('alias-name') + " is must be atleast 5 in length.");
            return false;
        } 

        if(fails == 0) {
            return true;
        }
    }

    function validate() {
        fails = 0;
        $("#formContainer :input").each(function() {
            if($(this).attr('type') !="hidden" && $(this).attr('type') !="button" && $(this).attr('type') !="submit"){
                if(!validateFromAttribute($(this))){
                    return false;
                }else{
                    $(this).parent().removeClass('has-error').addClass('has-success');
                }
            }
        });
        if (fails > 0) {
            return false
        }else {
            return true
        }
    }
</script>