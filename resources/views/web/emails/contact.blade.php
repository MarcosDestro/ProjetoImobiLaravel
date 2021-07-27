@component('mail::message')
# Novo Contato:

<p>Nome: {{$name}} <{{ $email }}></p>
<p>Telefone: {{$cell}}</p>
<p>Mensagem:</p>
<p>{{ $message }}</p>

* Este email é enviado automaticamente através do sistema!
@endcomponent