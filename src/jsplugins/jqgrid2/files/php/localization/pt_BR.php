<?php
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
class jqEventLocalization {

//fullcalender translations
// these locale setting use javascript convensions for date forrmating
/*
 * !!!!!!!!! NOTE dateFormat in fullcalender differ from datepicker
 * Configure both in appropriate way
 */

public $dateFormat = "dd/MM/yyyy";

public $fullcalendar = array(
	'isRTL'=> false,
	'firstDay' => 1,
	'monthNames'=>array('Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'),
	'monthNamesShort'=>array('Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'),
	'dayNames'=> array('Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sabado'),
	'dayNamesShort'=>array('Dom','Seg','Ter','Qua','Qui','Sex','Sab'),
	'buttonText'=>array (
		'prev'=> '&nbsp;&#9668;&nbsp;',
		'next'=> '&nbsp;&#9658;&nbsp;',
		'prevYear'=> '&nbsp;&lt;&lt;&nbsp;',
		'nextYear'=> '&nbsp;&gt;&gt;&nbsp;',
		'today' =>'hoje',
		'month' =>'mês',
		'week' =>'semana',
		'day'=> 'dia',
		'search'=>'pesquisar'
	),
	'allDayText'=> 'dia inteiro',
	'axisFormat'=> 'h(:mm)tt',
	'timeFormat'=>array(
		'agenda'=> 'h:mm{ - h:mm}'
	)
);

//Time picker
// slotMinutes: 30 - this is get from calender options
// the corresponding options is timeInterval
public $timepicker = array(
	"minutes"=>"mins",
	"onehour"=>"1 hr",
	"hours"=>"hrs"
);
// Timepicker search dialog
public $timepicker_lang = array(
	'timeOnlyTitle' =>'Escolha um Horário',
	'timeText'=>'Tempo',
	'hourText'=> 'Hora',
	'minuteText'=> 'Minuto',
	'secondText'=> 'Segundo',
	'currentText'=> 'Agora',
	'closeText'=> 'Concluir',
	'ampm'=> false
);

function __construct() {
	if($this->use_datepicker_lang === false)
	{
		$this->setDatepickerLang();
	}
}

// Datepicker translations.
// We do this here since we can not determine the current datepicker pranslation
public $use_datepicker_lang = false;

public $datepicker_lang = array();
public function setDatepickerLang( )
{
	$this->datepicker_lang= array(
		'closeText'=>'Concluído',
		'prevText'=>'Ant',
		'nextText'=>'Prox',
		'currentText'=>'Hoje',
		'showMonthAfterYear'=> false,
		'yearSuffix'=> '',
		'dayNamesMin' => array('Do','Se','Te','Qua','Qui','Se','Sa'),
		'weekHeader'=> 'Sem',
		'dateFormat'=> 'dd/mm/yy', // THE MOST IMPORTANT DIFFERENCE FROM full calender.
		// Do not translate these - do it in fullcalender
		'monthNames' => $this->fullcalendar['monthNames'],
		'monthNamesShort'=> $this->fullcalendar['monthNamesShort'],
		'dayNames'=> $this->fullcalendar['dayNames'],
		'dayNamesShort'=> $this->fullcalendar['dayNamesShort'],
		'firstDay'=> $this->fullcalendar['firstDay'],
		'isRTL'=> $this->fullcalendar['isRTL']
	);
}

//  buttons left
public $button_search = "Pesquiase";
public $button_user = "Calendário";
public $button_export = "Exportar";
public $button_print = "Imprimir";

public $captionchangeusr = "Mudar Calendário";
public $currentcalendar ="Calendário Atual";

// Search
public $find = 'Procurar';
public $close = 'Fechar';
public $captionsearch = "Busca Encontrada";
public $found_events = "Encontrado";
public $header_search = "Eventos de Busca";
// do not tuch the order
public $searchopers = array(
	'Igual',
	'Diferente',
	'Menor',
	'Menor ou Igual',
	'Maior',
	'Maior ou Igual',
	'Começa com',
	'Não Começa com',
	'Está Contido',
	'Não Está Contido',
	'Termina com',
	'Não Termina com',
	'Contém',
	'Não Contém',
	'É Nulo',
	'Não É Nulo'
);

// Form Fields
public $form_title = "Título";
public $form_description = "Descrição";
public $form_start = "Começo";
public $form_end = "Fim";
public $form_location = "Localização";
public $form_categories= "Categoria";
public $form_access = "Acesso";
public $form_all_day = "Dia Inteiro";

// Form Buttons
public $add = "Adicionar";
public $save = "Salvar";
public $remove = "Remover";
public $change = "Mudar";
public $cancel = "Cancelar";
//Form Titles
public $captionedit = "Editar Evento";
public $captionadd = "Novo Evento";

// options categories.
// translate the value and not the keys.
public $option_categories = array(
	"personal"=>"Pessoal",
	"work"=>"Trabalho",
	"family"=>"Família",
	"holiday"=>"Feriado"
);
// if you add or delete in option category change the array bellow accordandly
public $categories_css = array(
	'personal' => '#c0c0c0',
        'work' => '#ff0000',
        'family' => '#00ff00',
        'holiday' => '#ff6600'
);

public $option_access = array(
	"public"=>"Público",
	"private"=>"Privado"
);

//Swich user form
public $label_user = "Selecione um Calendário";
public $title_user = "Mudar o calendário";

}
?>