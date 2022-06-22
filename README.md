<a href="https://jphn.github.io/beacademy-devstart-frontend-myportfolio/"><img src="https://raw.githubusercontent.com/Jphn/beacademy-devstart-frontend-myportfolio/29c391f17766dd2cb19fff140c14fd9b70f7ce14/assets/img/dna-svgrepo-com.svg" align="right" width="60"/></a>

# PHP TEMPLATE ENGINE

## Sobre

Este é um simples projeto feito por mim, com o objetivo de desenvolver uma simples template engine para uso próprio em futuros projetos.

> O projeto ainda está em uma fase bem inicial, então ainda não possui muitas funcionalidades. A engine foi desenvolvida na versão `8.1.6` do PHP, então podem haver incompatibilidades com versões anteriores.

## Exemplo de uso

### view.html

```html
<!-- Global scope variables -->
<h1>{{ title }}</h1>

<!-- If function -->
{{ ?if subtitle }}
<h2>{{ subtitle }}</h2>
{{ ?/if}}

<!-- Foreach function -->
<ul>
 {{ ?foreach array }}
 <li>{{ this.index }} {{ this.value }}<li>
 {{ ?/foreach }}
</ul>
<!-- Works fine with associative arrays too -->
<ul>
 {{ ?foreach associativeArray }}
 <li>{{ this.index }} {{ this.name }} {{ this.age }}<li>
 {{ ?/foreach }}
</ul>
```

### index.php

```php
$data = [
 'title' => 'Sample Title',
 'subtitle' => 'Subtitle',

 'array' => [
  'First Name',
  'Second Name',
  'Third Name'
 ],

 'associativeArray' => [
  [
  'name' => 'First Name',
  'age' => 99   
  ],
  [
  'name' => 'Second Name',
  'age' => 99   
  ],
  [
  'name' => 'Third Name',
  'age' => 99   
  ]
 ]
];

$view = file_get_contents(...);

echo Engine::render($view, $data);
```

### Saída

```html
<h1>Sample Title</h1>

<h2>Subtitle</h2>

<ul>
 <li>1 First Name</li>
 <li>2 Second Name</li>
 <li>3 Third Name</li>
</ul>
<ul>
 <li>1 First Name 99</li>
 <li>2 Second Name 99</li>
 <li>3 Third Name 99</li>
</ul>
```
