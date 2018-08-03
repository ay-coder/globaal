<div class="box-body">
    <div class="form-group">
        {{ Form::label('level_of_experience', 'Level Of Experience :', ['class' => 'col-lg-2 control-label']) }}
        <div class="col-lg-10">
            {{ Form::text('level_of_experience', null, ['class' => 'form-control', 'placeholder' => 'Level Of Experience', 'required' => 'required']) }}
        </div>
    </div>
</div>