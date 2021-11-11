package com.horecarobot.robotcommunication.Controllers;

import com.horecarobot.robotcommunication.Messages.Command;

import org.springframework.messaging.handler.annotation.DestinationVariable;
import org.springframework.messaging.handler.annotation.MessageMapping;
import org.springframework.messaging.handler.annotation.SendTo;
import org.springframework.stereotype.Controller;

import nonapi.io.github.classgraph.json.JSONSerializer;

@Controller
public class CommandController {
    @MessageMapping("/robot/{robotId}/request")
    @SendTo("/topic/robot/request")
    public String requestCommand(@DestinationVariable long robotId, Command command) {
        return robotId + " " + JSONSerializer.serializeObject(command);
    }

    @MessageMapping("/robot/{robotId}/response")
    @SendTo("/topic/robot/response")
    public String responseCommand(@DestinationVariable long robotId, Command command) {
        return robotId + " " + JSONSerializer.serializeObject(command);
    }
}
