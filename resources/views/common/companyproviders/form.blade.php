<div class="box-body">
    <div class="form-group">
        {{ Form::label('provider_id', 'Provider Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('provider_id', null, ['class' => 'form-control', 'placeholder' => 'Provider Id', 'required' => 'required']) }}
        </div>
    </div>
</div><div class="box-body">
    <div class="form-group">
        {{ Form::label('company_id', 'Company Id :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('company_id', null, ['class' => 'form-control', 'placeholder' => 'Company Id', 'required' => 'required']) }}
        </div>
    </div>
</div>