# About

## What

An opinionated PHP micro-framework to build modular applications.

For documentation, please visit [https://domynation.gitbook.io/domynation-framework/](https://domynation.gitbook.io/domynation-framework/).

## Why

This project originally started as a learning exercise to learn the intricacies of framework development. I then started using it for the [Sushi ERP](https://github.com/domynation/sushi-erp/) project, which over time grew big enough to justify spending more time and energy on polishing this framework. All in all, I did this **for fun**.

**But seriously, why burden us with yet another framework? What does doMynation bring to the table that others like Laravel and Symfony don't?**

In all honesty, **not much**. In any case, this framework is surely less secure, less performant, less battle-tested and less feature-complete than its competitors. The only thing that perhaps sets it apart is the architectural design and decisions that it imposes. To name a few:

1. The absence of controllers in the traditional sense, doMynation uses [Actions](routing-1.md#actions) instead
2. The emphasis on modularity and testability
3. The absence of [magic](https://www.freecodecamp.org/news/moving-away-from-magic-or-why-i-dont-want-to-use-laravel-anymore-2ce098c979bd/) and a strong emphasis on type safety

