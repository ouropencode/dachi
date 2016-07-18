# Dachi Web Framework - Roadmap

## Milestone 1 - v3
The following things need completing before we release V3 and go OSS

- Move helpers to separate repositories
- Find a way to stop all the 'sub packages' having to require dachi at a specific version
  - at the moment with every new 'minor' version update on dachi, all subpackages need their deps updating - this isn't ideal and should be looked at.
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
- Release to GitHub as OSS
