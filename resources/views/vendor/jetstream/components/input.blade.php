@props(['disabled' => false, 'field_require' => false,'field_check' => false])

<input {{ $disabled ? 'disabled' : '' }} {{ $field_require ? 'required' : '' }} {{ $field_check ? 'checked' : '' }}
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>