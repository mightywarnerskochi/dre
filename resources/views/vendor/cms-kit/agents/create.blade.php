@extends('cms-kit::layouts.cms')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('cms.agents.index') }}">Agents</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0">Add Agent</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('cms.agents.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('cms-kit::agents._form', ['buttonText' => 'Create Agent'])
        </form>
    </div>
</div>
@endsection
