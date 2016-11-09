# Dachi Web Framework - Roadmap

# v4
- replaced grunt with webpack
  this means we no longer push to S3? need to work that out.
  static assets also don't seem to get compiled

# v5+
The following things need completing before we release V3 and go OSS

- Move helpers to separate repositories
- Move radon-ui internal code to a separate package and rename to dachi-ui
- Rewrite CLI interface
- Full test AND doc coverage upon all existing public API functions
  - this must include full test coverage on all the 'sub packages'
    - dachi-datatables
	- dachi-permissions
	- dachi-email
	- dachi-files
	- dachi-ui
- Update example project
- Create more example projects
  - todo list
  - visitor guestbook
  - quiz
  - ...
