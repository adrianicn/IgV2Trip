@section('eventosAtraccion')	

@if(count($eventos)>0)
<div class="widget banner-slider box">
    <div class="owl-carousel" style="background-color: #EDF6FF" data-itemsPerDisplayWidth="[[0, 1], [480, 2], [768, 1], [992, 1], [1200, 1]]" data-items="1">

        @foreach ($eventos as $event)
        @if($Imgeventos!=null)
        @foreach ($Imgeventos as $img)
        @if($img->id_auxiliar==$event->id)

        <a href="#">
            <div class="shortcode-banner">
                <img src="{{ asset('public/images/icon/'.$img->filename)}}" alt="{!!$event->nombre_evento!!}">
                <div class="shortcode-banner-content">
                    <h4 class="banner-title">{!!$event->nombre_evento!!}</h4>
                    <div class="details">
                        
                        <pre class="mores">{!!$event->descripcion_evento!!}</pre>
                        
                            <?php $date = date_create($event->fecha_desde);
                                    $date2 = date_create($event->fecha_hasta);
                                                             ?>
                              <span class="comment-date" style="color: #EB5D5E">{!!date_format($date, 'j F ')!!}-{!!date_format($date2, 'j F ')!!}</span>
                    </div>
                    
                </div>
            </div>
        </a>
        @endif
        @endforeach
        @endif

        @endforeach






    </div>
    <div class="banner-text">
        <h2 class="banner-title">{{ trans('publico/labels.label24')}}</h2>

    </div>
</div>
@endif

@endsection

