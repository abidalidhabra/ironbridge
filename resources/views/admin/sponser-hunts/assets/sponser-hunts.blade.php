<script>
/** Add Hunt **/
let huntURL = "{{ route('admin.sponser-hunts.hunt-html') }}"
$(document).on('click', '.add-hunt', function() {
    if(validate()){
        let lastIndex = parseInt($('.hunt-container').children().last().attr('index'));
        $.get(huntURL, {index: lastIndex}, function(response) {
            $('.hunt-container').append(response);
        });
        $(this).removeClass('btn-success add-hunt').addClass('btn-danger remove-hunt').text('-');
    }
});
$(document).on('click', '.remove-hunt', function() {
    $(this).parent().parent().remove();
});


/** Add Clue **/
let clueURL = "{{ route('admin.sponser-hunts.clue-html') }}"
$(document).on('click', '.add-clue', function() {
    if(validate()){
        let huntElem = $(this).parents().closest('.single-hunt-container');
        let parentIndex = parseInt(huntElem.attr('index')-1);
        let lastIndex = parseInt($('.clue-container').children().last().attr('index'));
        $.get(clueURL, {index: lastIndex, parentIndex: parentIndex}, function(response) {
            $(huntElem).children('.clue-container').append(response);
        });
        $(this).removeClass('btn-success add-clue').addClass('btn-danger remove-clue').text('-');
    }
});
$(document).on('click', '.remove-clue', function() {
    $(this).parent().parent().remove();
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