/**
 * @author infinitydevphp <infinitydevphp@gmail.com>
 */

function hideBlockShowBlockChecked(el) {
    var checked = el.checked,
        $block = $('.block-hide').has(el),
        $currentBlock = $block.find('.hideInBlock'),
        $currentBlockInverse = $block.find('.showInBlock');

    if (checked) {
        $currentBlock.fadeIn(500);
        $currentBlockInverse.fadeOut(500);
    } else {
        $currentBlock.fadeOut(500);
        $currentBlockInverse.fadeIn(500);
    }
}

$(document).ready(function () {
    $('.block-hide [type="checkbox"].checkbox-toggle').each(function () {
        var $this = $(this);
        hideBlockShowBlockChecked(this);
        $this.on('change', function () {
            hideBlockShowBlockChecked(this);
        })
    });


    $('form').on('beforeSubmit', function (event) {
        event.preventDefault();

        var $form = $('form'),
            $formAnswer = $form.find('[name^=answers]');

        if (!$formAnswer.size()) {
            $formAnswer = $('[name^=answers]');

            if ($formAnswer.size()) {
                $formAnswer.each(function () {
                    $form.append($(this).clone().css('display', 'none'));
                })
            }
        }
    })
});