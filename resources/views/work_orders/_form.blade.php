<div class="form-group">
    <label for="customer_id">顧客 <span style="color:#dc2626">*</span></label>
    <select id="customer_id" name="customer_id" required>
        <option value="">-- 選択してください --</option>
        @foreach($customers as $customer)
            <option value="{{ $customer->id }}" @selected(old('customer_id', $workOrder->customer_id ?? '') == $customer->id)>
                {{ $customer->name }}
            </option>
        @endforeach
    </select>
    @error('customer_id') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="title">件名 <span style="color:#dc2626">*</span></label>
    <input type="text" id="title" name="title" value="{{ old('title', $workOrder->title ?? '') }}" required>
    @error('title') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="description">詳細</label>
    <textarea id="description" name="description">{{ old('description', $workOrder->description ?? '') }}</textarea>
    @error('description') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="status">ステータス <span style="color:#dc2626">*</span></label>
    <select id="status" name="status" required>
        @foreach(\App\Http\Controllers\WorkOrderController::STATUSES as $s)
            <option value="{{ $s }}" @selected(old('status', $workOrder->status ?? '未着手') === $s)>{{ $s }}</option>
        @endforeach
    </select>
    @error('status') <div class="error">{{ $message }}</div> @enderror
</div>

<div class="form-group">
    <label for="due_date">納期</label>
    <input type="date" id="due_date" name="due_date" value="{{ old('due_date', isset($workOrder) && $workOrder->due_date ? $workOrder->due_date->format('Y-m-d') : '') }}">
    @error('due_date') <div class="error">{{ $message }}</div> @enderror
</div>
