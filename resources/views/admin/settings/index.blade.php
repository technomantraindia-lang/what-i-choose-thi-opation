@extends('admin.layouts.app')
@section('title', 'Settings')
@section('content')
<h2 class="mb-4">Store Settings</h2>
<div class="card"><div class="card-body">
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">@csrf @method('PUT')
        <h5 class="mb-3">General</h5>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Website Name</label><input type="text" name="company_name" class="form-control" value="{{ $settings['company_name'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Logo</label><input type="file" name="logo" class="form-control" accept="image/*">
                @if(!empty($settings['logo']))<img src="{{ asset('storage/'.$settings['logo']) }}" class="mt-2" style="max-height:60px;">@endif
            </div>
            <div class="col-md-6 mb-3"><label class="form-label">Contact Email</label><input type="email" name="company_email" class="form-control" value="{{ $settings['company_email'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Phone Number</label><input type="text" name="company_phone" class="form-control" value="{{ $settings['company_phone'] ?? '' }}"></div>
            <div class="col-md-12 mb-3"><label class="form-label">Address</label><textarea name="company_address" class="form-control" rows="2">{{ $settings['company_address'] ?? '' }}</textarea></div>
            <div class="col-md-4 mb-3"><label class="form-label">GST Number</label><input type="text" name="gst_number" class="form-control" value="{{ $settings['gst_number'] ?? '' }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Currency</label><input type="text" name="currency" class="form-control" value="{{ $settings['currency'] ?? 'INR' }}"></div>
            <div class="col-md-4 mb-3"><label class="form-label">Order Prefix</label><input type="text" name="order_prefix" class="form-control" value="{{ $settings['order_prefix'] ?? 'MF' }}"></div>
        </div>
        <hr><h5 class="mb-3">Social Links</h5>
        <div class="row">
            <div class="col-md-6 mb-3"><label class="form-label">Facebook URL</label><input type="url" name="facebook_url" class="form-control" value="{{ $settings['facebook_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Instagram URL</label><input type="url" name="instagram_url" class="form-control" value="{{ $settings['instagram_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">Twitter URL</label><input type="url" name="twitter_url" class="form-control" value="{{ $settings['twitter_url'] ?? '' }}"></div>
            <div class="col-md-6 mb-3"><label class="form-label">WhatsApp Number</label><input type="text" name="whatsapp_number" class="form-control" value="{{ $settings['whatsapp_number'] ?? '' }}"></div>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</div></div>
@endsection
