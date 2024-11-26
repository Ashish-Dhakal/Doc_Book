<select id="{{ $name }}" name="{{ $name }}" class="block mt-1 w-full" {{ $attributes }}>
    @foreach ($options as $value => $label)
        <option value="{{ $value }}" {{ old($name, $value) == $value ? 'selected' : '' }}>
            {{ $label }}
        </option>
    @endforeach
</select>
