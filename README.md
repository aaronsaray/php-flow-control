# PHP Flow Control

This library assists with creating a flow control or process flow in PHP.  It really is just a scaffolding for developing
your own workflow process in PHP.

## Installation Instructions

*coming soon*

## Documentation

### Getting Started

The goal here is to remember that each work flow (point) should only know about the next possible scenario.  These
particular ones were not built yet to go backwards. :)  With this in mind, use this scaffolding to create a PHP flow control.

If you think about it as a tree going to the next step, that will help. You can have 1-n possible outcomes for the 'next' 
point to go to.  

Points have payloads which determine the thing to take action on.  This could be an object retrieved from a database (imagine a
user-built system that handles user-determined flows), or a single URL for redirect requests in your controller.

### An Example

In this particular example, we're going to make a multi-step wizard.  We know the current order of the steps, but the business
rules sometimes changes. Sometimes certain steps must happen first - other times the object we're updating might have different
properties that are needed.

In this example, we're going to have three steps:
- What is your name? - which is asked at /question/name
- How old are you? - which is asked at /question/old?id=question_id
- Do you like cake? - which is asked at /do-you-like-cake?id=question_id

Finally, we'll see the results at /your/results/are/here/question_id

```php
class Payload implements PayloadInterface
{
  protected $url;
  
  public function __construct($url)
  {
    $this->url = $url;
  }
  
  public function get()
  {
    return $this->url;
  }
}
```

This Payload is a very simple object.  All it does is pass a single string url back.  This payload could be very advanced,
based on your business rules.  This object is not limited to just working with strings - it could return a whole new object
or even dictate the application progresses to a different flow.

```php
class Director extends DirectorAbstract
{
  public function getFirstAllowed()
  {
    $this->setPoint(new Point\WhatsYourName());
    return $this->getPoint();
  }
}
```

Each workflow consists of points and a director.  The director's main job is to indicate what the start of the workflow is,
like this example.  The rest of the functionality is in the abstract.  In this case, the first step to our process is this point.

```php
class Question implements ProcessableInterface
{
  public $id;
  public $name;
  public $age;
  $public $cake;
}
```

This class is the object that we're tracking.  Super complicated, I know...

```php
class WhatsYourName extends PointAbstract
{
  public function getPayload()
  {
    return new Payload('/question/name');
  }
  
  public function next(ProcessableInterface $question)
  {
    if (empty($question->name)) throw new IllegalPointException();
    
    return new WhatsYourAge($question);
  }
}
```

This is where you begin to see the power of the workflow.  The get payload creates a payload object that represents the current
state.  In our example, our state is a URL reference - so this is the URL for this particular point.

The next() method call passes in the question object.  From there, it does business logic to determine if we can go to the
next point (or question).  If there is no business validation necessary, you could simply just return the next point.  In
this particular case, we don't want to ask about the age (in the next point) until we have a name.  We assume that sometime
in the runtime of this particular call, a name was assigned to the question.

```php
class WhatsYourAge extends PointAbstract
{
  protected $question;
  
  public function __construct($question) 
  {
    $this->question = $question;
  }
  
  public function getPayload()
  {
    return new Payload('/question/age?id=' . $this->question->id);
  }
  
  public function next(ProcessableInterface $question)
  {
    if (empty($question->age)) throw new IllegalPointException();
    
    return new YaLikeCake($this->question);
  }
}
```

Here you can see an example of how the payload might be altered by the particular processable object.

From here, the rest of the objects look the same - with the exception of any point that ends a tree/workflow.  This one 
does not need to have the next() method - as it should only throw an exception to indicate that there are no more points.
(The abstract already does this).

So next, in our example, let's begin the request.  We want to ask for the first point and put it in a link.

```php
$director = new Director();
$url = $director->getFirstAllowed()->getPayload()->get();
echo "<a href='{$url}'>Begin the questions!</a>";
```

Imagine a scenario where the user could drop off from the workflow - but should be able to pick it up again?  You'd call
the getLastAllowed() method to get the last one that they could possibly access.  For example, if the user answered their
name and their age, the following example would return a link to `/do-you-like-cake?id=question_id` based on our example.

```php
$question = retrieveQuestionFromSomePreviousState();
$director = new Director();
$url = $director->getLastAllowed($question)->getPayload()->get();
echo "<a href='{$url}'>Finish up the questions!</a>";
```

During the various steps, you will need to interact with the points.  Imagine the scenario after a successful submit of
the name question:

```php
$question = new Question();
$question->name = $_POST['name'];
$director = new Director();
$nextUrl = $director->getNextPoint()->getPayload()->get();
die(header("Location: {$nextUrl}"));
```

This allows your controller to understand that it should assign values or do any other controller-logic, but it doesn't
necessarily need to know where it's sending the workflow next.

## About

I was creating a multi-step application again - a wizard based system - and I kept thinking that I do this alot, I wish
there was an easier way to do this.  It got even worse when the steps / order of the steps change.  I decided to create
this scaffolding as a way to develop your own process or workflow control in PHP.

