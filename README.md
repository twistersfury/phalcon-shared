PHP7 Phalcon Shared Library

This library is intended to exist as an enhancement to the already great Phalcon Framework. Eventually, some of the the feature in here will be added to the actual cphalcon repo.
 
**Note:** This system assumes that you include vendor after setting up the phalcon Di.

**\TwistersFury\Phalcon\Shared\Di\AbstractFactory** - Extends Phalcon FactoryDefault. Adds The Ability To 'Auto Register' Any Methods That Start With 'register'. Priority can be changed via AbstractFactory::priorityServices
 
**\TwistersFury\Phalcon\Shared\Di\FactoryDefault** - Extends AbstractFactory. Adds a few enhancements to some of the default services.
- URL Base Of Domain //domain.name.com
- Config With Dist Logic
- Registers Databases From Config
- Registers Volt Engine Setting Compile Defaults Based Off Constants
- Registers Crypt Using Key File From Config As Key

**\TwistersFury\Phalcon\Shared\Helpers\Defines** - Allows Calling '\define' with existing defines and/or callback functions. Useful to ensure define not called twice.

**\TwistersFury\Phalcon\Shared\Helpers\PathManager** - Gives Central Location To Set/Configure Project Paths

**Events**
- *twistersfury:static-defines* - Used to define static constants that do not depend on other static constants.
- *twistersfury:dynamic-defines* - Used to define constants that change based off runtime configuration or rely on other constants.

**Defines**
- *TF_DEBUG_MODE_DISABLED* - Debug Mode Is Disabled
- *TF_DEBUG_MODE_TESTING*  - Debug Mode Is Currently In Testing
- *TF_SHARED_SOURCE* - Library Source (src) Folder
- *TF_SHARED_PROJECT* - Library Root/Project Folder
- *TF_SHARED_TESTS* - Library Test (tests) Folder
- *TF_APP_ROOT* - Project Root Folder (../../../app In Project, ../ In Repo/Testing);
- *TF_DEBUG_MODE* - Current Debug Mode - Defaults To ENV(TF_DEBUG_MODE) or TF_DEBUG_MODE_DISABLED.

author Phoenix <phoenix@twistersfury.com>
license http://www.opensource.org/licenses/mit-license.html MIT License
copyright 2017 Twister's Fury