<div class="form-group">
    <label for="name">名前 <span style="color:#dc2626">*</span></label>
    <input type="text" id="name" name="name" value="{{ old('name', $customer->name ?? '') }}" required>
    @error('name') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="phone">電話番号</label>
    <input type="text" id="phone" name="phone" value="{{ old('phone', $customer->phone ?? '') }}">
    @error('phone') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="email">メールアドレス</label>
    <input type="email" id="email" name="email" value="{{ old('email', $customer->email ?? '') }}">
    @error('email') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="address">住所</label>
    <textarea id="address" name="address">{{ old('address', $customer->address ?? '') }}</textarea>
    @error('address') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="note">備考</label>
    <textarea id="note" name="note">{{ old('note', $customer->note ?? '') }}</textarea>
    @error('note') <div class="error">{{ $message }}</div> @enderror
</div>
