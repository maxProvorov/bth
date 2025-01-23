@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Products</h1>
    <button class="btn btn-primary open-modal" data-edit="false">Add Product</button>
</div>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Article</th>
            <th>Name</th>
            <th>Status</th>
            <th>Data</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td><img style="width: 120px;" src="https://placehold.co/600x400" alt=""></td>
                <td>{{ $product->article }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->status }}</td>
                <td> 
                    @if($product->data)
                        @foreach($product->data as $key => $value)
                            <p>{{ $key }}: {{ $value }}</p>
                        @endforeach
                    @endif
                <td>
                    <button class="btn btn-success btn-sm open-modal" 
                        data-edit="true"
                        data-url="{{ route('products.update', $product) }}"
                        data-name="{{ $product->name }}"
                        data-article="{{ $product->article }}"
                        data-status="{{ $product->status }}"
                        data-allow-edit-article="{{ auth()->user()->role  ===  config('products.role.admin') ? 'true' : 'false' }}"
                        data-data="{{ json_encode($product->data) }}">
                        Edit
                    </button>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                    </form>
                    <a href="{{ route('products.show', $product) }}" class="ml-5">View</a>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Модальное окно -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add/Edit Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="_method" id="hiddenMethod" value="PUT">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required minlength="10">
                    </div>
                    <div class="mb-3">
                        <label for="article" class="form-label">Article</label>
                        <input type="text" id="article" name="article" class="form-control" required pattern="[a-zA-Z0-9]+">
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" name="status" class="form-select">
                            <option value="available">Available</option>
                            <option value="unavailable">Unavailable</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Data</label>
                        <div id="dataFields"></div>
                        <button type="button" id="addDataField" class="btn btn-primary btn-sm">Add Field</button>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));

    // Функция для добавления нового поля key-value
    function addDataField(key = '', value = '') {
        const dataContainer = document.getElementById('dataFields');
        const fieldGroup = document.createElement('div');
        fieldGroup.className = 'input-group';

        fieldGroup.innerHTML = `
            <input type="text" name="data_keys[]" class="form-control" placeholder="Key" value="${key}">
            <input type="text" name="data_values[]" class="form-control" placeholder="Value" value="${value}">
            <button type="button" class="btn btn-danger btn-sm remove-data-field">Remove</button>
        `;

        dataContainer.appendChild(fieldGroup);

        // Удаление поля
        fieldGroup.querySelector('.remove-data-field').addEventListener('click', function () {
            fieldGroup.remove();
        });
    }

    // Открытие модального окна
    document.querySelectorAll('.open-modal').forEach(button => {
        button.addEventListener('click', function () {
            const isEdit = this.dataset.edit === 'true';
            const form = document.getElementById('productForm');
            const allowEditArticle = this.getAttribute('data-allow-edit-article') === 'true';

            // Устанавливаем URL(для создания или обновления)
            form.action = isEdit ? this.dataset.url : '{{ route('products.store') }}';

            // Добавляем скрытое поле для PUT, если редактируем
            document.getElementById('hiddenMethod').value = isEdit ? 'PUT' : '';

            // Заполняем поля
            document.getElementById('name').value = isEdit ? this.dataset.name : '';
            document.getElementById('article').value = isEdit ? this.dataset.article : '';
            document.getElementById('status').value = isEdit ? this.dataset.status : 'available';
            document.getElementById('article').readOnly = !allowEditArticle &&  isEdit;

            // Очищаем контейнер данных и заполняем, если редактируем
            const dataContainer = document.getElementById('dataFields');
            dataContainer.innerHTML = '';
            if (isEdit && this.dataset.data) {
                const data = JSON.parse(this.dataset.data);
                if (data && typeof data === 'object') {
                    Object.entries(data).forEach(([key, value]) => {
                        addDataField(key, value);
                    });
                }
            }

            productModal.show();
        });
    });

    // Добавление нового поля для данных
    document.getElementById('addDataField').addEventListener('click', function () {
        addDataField();
    });
});
</script>