<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'The must be accepted.',
    'accepted_if' => 'The must be accepted when :other is :value.',
    'active_url' => 'The is not a valid URL.',
    'after' => 'The must be a date after :date.',
    'after_or_equal' => 'The must be a date after or equal to :date.',
    'alpha' => 'The must only contain letters.',
    'alpha_dash' => 'The must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'The must only contain letters and numbers.',
    'array' => 'The must be an array.',
    'before' => 'The must be a date before :date.',
    'before_or_equal' => 'The must be a date before or equal to :date.',
    'between' => [
        'array' => 'The must have between :min and :max items.',
        'file' => 'The must be between :min and :max kilobytes.',
        'numeric' => 'The must be between :min and :max.',
        'string' => 'The must be between :min and :max characters.',
    ],
    'boolean' => 'The field must be true or false.',
    'confirmed' => 'The confirmation does not match.',
    'current_password' => 'The password is incorrect.',
    'date' => 'The is not a valid date.',
    'date_equals' => 'The must be a date equal to :date.',
    'date_format' => 'The does not match the format :format.',
    'declined' => 'The must be declined.',
    'declined_if' => 'The must be declined when :other is :value.',
    'different' => 'The and :other must be different.',
    'digits' => 'The must be :digits digits.',
    'digits_between' => 'The must be between :min and :max digits.',
    'dimensions' => 'The has invalid image dimensions.',
    'distinct' => 'The field has a duplicate value.',
    'email' => 'The must be a valid email address.',
    'ends_with' => 'The must end with one of the following: :values.',
    'enum' => 'The selected is invalid.',
    'exists' => 'The selected is invalid.',
    'file' => 'The must be a file.',
    'filled' => 'The field must have a value.',
    'gt' => [
        'array' => 'The must have more than :value items.',
        'file' => 'The must be greater than :value kilobytes.',
        'numeric' => 'The must be greater than :value.',
        'string' => 'The must be greater than :value characters.',
    ],
    'gte' => [
        'array' => 'The must have :value items or more.',
        'file' => 'The must be greater than or equal to :value kilobytes.',
        'numeric' => 'The must be greater than or equal to :value.',
        'string' => 'The must be greater than or equal to :value characters.',
    ],
    'image' => 'The must be an image.',
    'in' => 'The selected is invalid.',
    'in_array' => 'The field does not exist in :other.',
    'integer' => 'The must be an integer.',
    'ip' => 'The must be a valid IP address.',
    'ipv4' => 'The must be a valid IPv4 address.',
    'ipv6' => 'The must be a valid IPv6 address.',
    'json' => 'The must be a valid JSON string.',
    'lt' => [
        'array' => 'The must have less than :value items.',
        'file' => 'The must be less than :value kilobytes.',
        'numeric' => 'The must be less than :value.',
        'string' => 'The must be less than :value characters.',
    ],
    'lte' => [
        'array' => 'The must not have more than :value items.',
        'file' => 'The must be less than or equal to :value kilobytes.',
        'numeric' => 'The must be less than or equal to :value.',
        'string' => 'The must be less than or equal to :value characters.',
    ],
    'mac_address' => 'The must be a valid MAC address.',
    'max' => [
        'array' => 'The must not have more than :max items.',
        'file' => 'The must not be greater than :max kilobytes.',
        'numeric' => 'The must not be greater than :max.',
        'string' => 'The must not be greater than :max characters.',
    ],
    'mimes' => 'The must be a file of type: :values.',
    'mimetypes' => 'The must be a file of type: :values.',
    'min' => [
        'array' => 'The must have at least :min items.',
        'file' => 'The must be at least :min kilobytes.',
        'numeric' => 'The must be at least :min.',
        'string' => 'The must be at least :min characters.',
    ],
    'multiple_of' => 'The must be a multiple of :value.',
    'not_in' => 'The selected is invalid.',
    'not_regex' => 'The format is invalid.',
    'numeric' => 'The must be a number.',
    'password' => [
        'letters' => 'The must contain at least one letter.',
        'mixed' => 'The must contain at least one uppercase and one lowercase letter.',
        'numbers' => 'The must contain at least one number.',
        'symbols' => 'The must contain at least one symbol.',
        'uncompromised' => 'The given has appeared in a data leak. Please choose a different :attribute.',
    ],
    'present' => 'The field must be present.',
    'prohibited' => 'The field is prohibited.',
    'prohibited_if' => 'The field is prohibited when :other is :value.',
    'prohibited_unless' => 'The field is prohibited unless :other is in :values.',
    'prohibits' => 'The field prohibits :other from being present.',
    'regex' => 'The format is invalid.',
    'required' => 'The field is required.',
    'required_array_keys' => 'The field must contain entries for: :values.',
    'required_if' => 'The field is required when :other is :value.',
    'required_unless' => 'The field is required unless :other is in :values.',
    'required_with' => 'The field is required when :values is present.',
    'required_with_all' => 'The field is required when :values are present.',
    'required_without' => 'The field is required when :values is not present.',
    'required_without_all' => 'The field is required when none of :values are present.',
    'same' => 'The and :other must match.',
    'size' => [
        'array' => 'The must contain :size items.',
        'file' => 'The must be :size kilobytes.',
        'numeric' => 'The must be :size.',
        'string' => 'The must be :size characters.',
    ],
    'starts_with' => 'The must start with one of the following: :values.',
    'string' => 'The must be a string.',
    'timezone' => 'The must be a valid timezone.',
    'unique' => 'The has already been taken.',
    'uploaded' => 'The failed to upload.',
    'url' => 'The must be a valid URL.',
    'uuid' => 'The must be a valid UUID.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
