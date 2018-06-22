@ extends('template.app')

@section('content')
<div class="col-md-12">
        <h3>Editar contato</h3>
    </div>

    <div class="col-md-6 well">
        <form class="col-md-12" action="{{url('/pessoas/update')}}" method="POST">
            {{csrf_field()}}
            <div class="from-group col-md-12">
                <label class="control-label">Nome:</label>
                <input name="nome" class="form-control" placeholder="Nome"> 
            </div>
        </form> 
    </div>       
@endsection