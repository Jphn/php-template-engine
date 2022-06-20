<a href="https://jphn.github.io/beacademy-devstart-frontend-myportfolio/"><img src="https://raw.githubusercontent.com/Jphn/beacademy-devstart-frontend-myportfolio/29c391f17766dd2cb19fff140c14fd9b70f7ce14/assets/img/dna-svgrepo-com.svg" align="right" width="60"/></a>

# PHP TEMPLATE ENGINE

## Sobre

Este é um simples projeto feito por mim, com o objetivo de desenvolver uma simples template engine para uso próprio em futuros projetos.

> O projeto ainda está em uma fase bem inicial, então ainda não possui muitas funcionalidades. A engine foi desenvolvida na versão `8.1.6` do PHP, então podem haver incompatibilidades com versões anteriores.

## Exemplo de uso

### view.html

```html
<h1>{{ title }}</h1>
<ul>
 {{ ?foreach data }}
 <li>{{ value }}<li>
 {{ ?/foreach }}
</ul>
```

### index.php

```php
$data = [
 'title' => 'Sample Title',
 [
  'value' => 'First Name'
 ],
 [
  'value' => 'Second Name'
 ],
 [
  'value' => 'Third Name'
 ]
];

echo Engine::render('view', $data);
```

### Saída

```html
<h1>Sample Title</h1>
<ul>
 <li>First Name<li>
 <li>Second Name<li>
 <li>Third Name<li>
</ul>
```
