Overall Strategy
================

Project migration from one framework to another is not a task, which can be done right away. Most likely you have spent
several months or even years to create your current codebase, and its update will also take much time. You will unlikely
to receive more then 2 weeks for the process start. It may also happen you will have no official time for the process at all.

That is why this package was developed. Creation of "Two-headed Beast" will not consume much of your time: it will take
one hour in case project does not twist Yii standard components and flow. If the business grands you dedicated time for
the migration process - use it to transfer most crucial features. Afterwards you will have to periodically pick up some
time to migrate another feature. You should be ready for the long-term processing running.

Try to reserve at least one day per week for another functionality transfer. If business does not allow spending entire
working day on project migration - consider an overtime. If it is not acceptable try to spend at least several hours per
week. Do not be afraid of slow progress - fear only to stand still.

Always implement new features using new framework. If new feature is coupled tight with some legacy code, try
to rewrite this code along with the new feature development.

Put the code rewriting into the features and bug-fixing estimations. If it does not affects too much code - the estimation
will highly increase, while each part of legacy code being removed grands your project new benefits.

Code transfer is better to perform by controllers or, at least, particular controller/action pairs. Try to convert Yii
controller into Laravel one, including related models and views. At the early beginning it is better to start from
most simple controllers, like the one serving "contact su" feature. It will allow you to get experience of the process
and raise self-assertion for the more complex tasks.

You may consider to use parts of Laravel inside Yii application. For example: you may switch usage of Yii Active Record in
favor of Laravel Eloquent, you may use Laravel helpers instead Yii ones and so on. If particular Yii controller or component
is too heavy to be transferred to Laravel right away - start from its refactoring, integrating Laravel components inside it.

Having automated functional tests will serve you well during the process. If you do not have them, consider to create ones.
You can use [Laravel Dusk](https://laravel.com/docs/5.8/dusk) for them. Right the test against Yii HTTP controller, only
after then transfer this controller to Laravel. Test will serve you as indicator that transfer was successful.

Try to remove any local configuration from Yii side, like database connection config and so on. The "bridge" components
from this package will help you to achieve this.

Remove any file from legacy code, which is no longer necessary. If particular Yii model is no longer in use - remove it,
if particular config file is no longer needed - remove it. Cleaning up the "legacy" directory should be your goal: it 
should become empty in the end.
