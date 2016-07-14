$phoneClass = 'phone_' . uniqid() . '_' . $form->id;
    $form->field($model, {field})->widget(PhoneInput::className(), [
        'options' => [
            'id' => 'phone_' . $form->id,
            'class' => 'form-control ' . $phoneClass
        ],
        'jsOptions' => [
            'preferredLanguage' => [
                'ua', 'ru'
            ],
            'excludeCountries' => [
                'br'
            ],
            'formatOnInit' => true,
            'geoIpLookup' => new JsExpression('function(callback) {
                  $.get("http://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                      var countryCode = (resp && resp.country) ? resp.country : "";
                      callback(countryCode);
                  });
             }'),
    //        'initialCountry' => ['uk', 'ru', 'md'],
            'nationalMode' => true,
            'numberType' => 'MOBILE',
            'autoHideDialCode' => false,
    //        'onlyCountries' => null,
    //        'separateDialCode' => true,
    //        'utilScript' => '',
            'customPlaceholder' => new JsExpression('function (selectedCountryPlaceholder, selectedCountryData) {
            console.log(arguments);
                return "+" + selectedCountryData.dialCode + " " + selectedCountryPlaceholder;
            }')
        ],
    ]);
    
    $this->registerJs(new JsExpression('(function ($) {
        $(\'#' . $form->id . '\').on(\'beforeSubmit\', function () {
            var $phone = $(\'.' . $phoneClass . '\');
            $phone.val(\'+\' + $phone.intlTelInput(\'getNumber\'));
        });
    })(jQuery);'));