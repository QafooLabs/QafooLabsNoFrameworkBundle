# Widgets

Symfonys subrequest/controller feature is awesome on its own, however when used
to build a widget based application several improvements would be nice:

- A widget should be a folder with controller, template, stylesheets and javascript code
  This would allow easily adding, changing and deleting widgets as well as effieciently
  loading all the requirements for the frontend.

- When subrequests do not use ESI or HInclude, there should be an optional API to allow batch
  loading data that is used in several widgets such as common entities (Users, Products, Content, ...)

- Switching widgets to render in-process, ESI or Hinclude should be simplified.

- Combination of widgets and regular controllers/subcontrollers should be possible.

The concepts of these ideas are discussed by Bastian Hofmann in [Marrying Front- and Backend](https://speakerdeck.com/bastianhofmann/marrying-front-with-back-end-2)
and are prototyped by Woodworker in [Preparbles](https://github.com/P2EE/preparables).
