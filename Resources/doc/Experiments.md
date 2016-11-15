# Experiments

Experiments are the ONGR Settings Bundles functionality that enables
the user to experiment with the content that is displayed to a particular
set of users, depending on the device that they are using. This is 
particularly useful for A/B testing, but is also desirable when there
is a need to display different content on different devices. 

## Usage

Starting using it is very simple: just go to the `Experiments` tab of
the settings panel provided by the bundle and create a new experiment.
You will see that the set of profiles and environment conditions need 
to be provided. The conditions of the experiment that can be set include
device type (desktop, mobile, etc.), device brand, client type (browser,
library, media player, etc.), a specific client ('IE', 'Firefox', etc.) 
and the OS of the user. 

> Note, that if a certain condition is not set, it is interpreted as `any`

Now that you have a set of defined experiments, just enable them in the
panel and the specified profiles of the experiments will be enabled 
automatically, provided the conditions of the experiment are met.

## How it works?

The experiments are saved to Elasticsearch as a special kind of settings.
There is an `ExperimentListener` class that is listening to `kernel.request`
events and it checks whether there are any active experiments and if there
are, it checks the conditions for all of them, determines which profiles need
to be enabled and saves them in a separate cookie. This value is then
being caught by the `RequestListener` class, merged with the value from the
profiles cookie and set to the `SettingsManager`. Once the experiments cookie 
is set this process is not being repeated, because the `ExperimentListener` 
checks that and the rest of the parsing is skipped.  
