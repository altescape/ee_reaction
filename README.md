EE Reaction
===========

###***This plugin is not yet ready for production use***

Reaction is a plugin to capture reactions to ExpressionEngine entries
At the moment it's a rough work in progress - but the idea is to have something where users can state their reaction to any kind of channel entry.

**For example:**

![Reactions](https://s3-eu-west-1.amazonaws.com/mwimages/github/popsugar-reactions.png)

  Your members or readers can state their reaction to an article or something they've seen or read with examples like...STAR, LOVE, SMILE, LOL, POOR.

The plugin will count the reactions and store in a database plus show in front end per entry.

## TODOS:

### Control Panel pages
1. ~~Add a control panel pages~~ Finess control panel pages. :)
2. Add CP page where admins can customise reactions and icons, plus reorder, etc.
3. Add CP page that outputs reports and analysis on what has been reacted to and by whom.

### Front-end
1. Output admin customised reactions to front-end.
2. Style it so it looks lovely.
3. Limit 1 reaction per entry for each user (though users can change reaction).
4. Output in variable pair so front-end designers can use data with their own markup.

For example:
```
  {exp:reaction
    id="1"
    reaction_one_image="img/r1.png"
    reaction_two_image="img/r1.png"
    reaction_three_image="img/r1.png"
    reaction_four_image="img/r1.png"
  }
    {reactions}
      <div class="reactions">
        <span class="icon">{reaction_image}</span>
        <span>{reaction_name}</span>
      </div>
    {/reactions}
    <div class="total">{reaction_total}</div>
  {/exp:reaction}
```

***This plugin is not yet ready for production use***
