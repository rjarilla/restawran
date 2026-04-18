<?php
// List all previously uploaded product images for selection in the form
$normalDir = public_path('assets/images/products/normal');
$images = [];
if (file_exists($normalDir)) {
    $images = array_values(array_filter(scandir($normalDir), function($file) {
        return !in_array($file, ['.', '..']) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file);
    }));
}
?>
<select name="ExistingProductImage" class="form-select mt-2">
    <option value="">-- Select Existing Image --</option>
    @foreach($images as $img)
        <option value="assets/images/products/normal/{{ $img }}">{{ $img }}</option>
    @endforeach
</select>
